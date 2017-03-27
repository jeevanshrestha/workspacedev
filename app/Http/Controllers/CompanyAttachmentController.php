<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers as Voy;
use App\CompanyAttachment;
use Illuminate\Support\Facades\Storage;
use Auth;
use DB;
use TCG\Voyager\Models\User;

class CompanyAttachmentController extends Voy\VoyagerBreadController
{
    //
  public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('browse_'.$dataType->name);

        $getter = $dataType->server_side ? 'paginate' : 'get';

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType

        $role = User::getUserRole(Auth::id());
         if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            $relationships = $this->getRelationships($dataType);

            if ($model->timestamps) {
                $dataTypeContent = call_user_func([$model->with($relationships)->latest(), $getter]);
            } else {
                $dataTypeContent = call_user_func([$model->with($relationships)->orderBy('id', 'DESC'), $getter]);
            }

            //Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = isBreadTranslatable($model);

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

  // POST BRE(A)D
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $attachment = $request->file('attachment');
      	$content = file_get_contents($attachment->getRealPath());
        $mime	=	$attachment->getMimeType();
        $request->merge(['attachment_body' => base64_encode($content)]);
        $request->merge(['mime_type' => $mime]);

        // Check permission
        Voyager::canOrFail('add_'.$dataType->name);

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        if($request->input('ajax')==true)
            return response()->json([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
        else
        return redirect()
            ->route("voyager.{$dataType->slug}.edit", ['id' => $data->id])
            ->with([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

 
    public function generateattachments( $id)
    {
         $slug = 'company-attachments';

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();


        $relationships = $this->getRelationships($dataType);
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $dataTypeContent = call_user_func([$model->with($relationships), 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }
       $disk = Storage::disk(config('voyager.storage.disk'));
   
       if(Storage::disk(config('voyager.storage.disk'))->exists($dataTypeContent->attachment))
       {
        $file = Storage::disk(config('voyager.storage.disk'))->get($dataTypeContent->attachment);

       
         //Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->attachment);
         // name = explode('/',$dataTypeContent->attachment);
         //  return  redirect($file);
       }
       else
       {
           // Storage::disk(config('voyager.storage.disk'))->exists(
          $file = base64_decode($dataTypeContent->attachment_body);
       }

            $name = explode('/',$dataTypeContent->attachment);
            $filename = $name[2];
            $filetype = $dataTypeContent->mime_type;
           
            header("Content-type: $filetype");
            header("Content-Disposition: attachment; filename=$filename");
            return \Response::make($file, 200, ['Content-Type' => $filetype]);
      }


    public function attachmentsbycompany(Request $request, $id)
    {
        $attachmentData = DB::table('company_attachments')->select('id','subject', 'attachment', 'mime_type')->get();
      //  $attachmentData 	= CompanyAttachment::where('company_id',$id)->get();

      //  echo json_encode($userData);

        return  response()->json($attachmentData);
    }
}
