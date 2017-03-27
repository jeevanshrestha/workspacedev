@extends('voyager::master')

@section('page_title','View '.$dataType->display_name_singular)

@section('page_header')
<h1 class="page-title">
  <i class="{{ $dataType->icon }}"></i> Viewing {{ ucfirst($dataType->display_name_singular) }} &nbsp;
  @if (Voyager::can('delete_'.$dataType->name))
  <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
    <span class="glyphicon glyphicon-pencil"></span>&nbsp;
    Edit
  </a>
  @endif
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')
<div class="page-content container-fluid">
  <div class="row">
    <div class="col-md-12">
      <ul class="nav nav-pills">
        <li class="active"><a data-toggle="pill" href="#company"><h5>Company Details</h5></a></li>
        <li><a data-toggle="pill" href="#users"><h5>Company Users</h5></a></li>
        <li><a data-toggle="pill" href="#attachments"><h5>Company Attachments</h5></a></li>
      </ul>

      <div class="tab-content">
        <div id="company" class="tab-pane fade in active">
         <div class="panel panel-bordered" style="padding-bottom:5px;">

          <!-- /.box-header -->
          <!-- form start -->

          @foreach($dataType->readRows as $row)
          @php $rowDetails = json_decode($row->details); @endphp

          <div class="panel-heading" style="border-bottom:0;">
            <h3 class="panel-title">{{ $row->display_name }}</h3>
          </div>

          <div class="panel-body" style="padding-top:0;">
            @if($row->type == "image")
            <img class="img-responsive"
            src="{{ Voyager::image($dataTypeContent->{$row->field}) }}">
            @elseif($row->type == 'select_dropdown' && property_exists($rowDetails, 'options') &&
            !empty($rowDetails->options->{$dataTypeContent->{$row->field}})
            )

            <?php echo $rowDetails->options->{$dataTypeContent->{$row->field}};?>
            @elseif($row->type == 'select_dropdown' && $dataTypeContent->{$row->field . '_page_slug'})
            <a href="{{ $dataTypeContent->{$row->field . '_page_slug'} }}">{{ $dataTypeContent->{$row->field}  }}</a>
            @elseif($row->type == 'select_multiple')
            @if(property_exists($rowDetails, 'relationship'))

            @foreach($dataTypeContent->{$row->field} as $item)
            @if($item->{$row->field . '_page_slug'})
            <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field}  }}</a>@if(!$loop->last), @endif
            @else
            {{ $item->{$row->field}  }}
            @endif
            @endforeach

            @elseif(property_exists($rowDetails, 'options'))
            @foreach($dataTypeContent->{$row->field} as $item)
            {{ $rowDetails->options->{$item} . (!$loop->last ? ', ' : '') }}
            @endforeach
            @endif
            @elseif($row->type == 'date')
            {{ $rowDetails && property_exists($rowDetails, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($rowDetails->format) : $dataTypeContent->{$row->field} }}
            @elseif($row->type == 'checkbox')
            @if($rowDetails && property_exists($rowDetails, 'on') && property_exists($rowDetails, 'off'))
            @if($dataTypeContent->{$row->field})
            <span class="label label-info">{{ $rowDetails->on }}</span>
            @else
            <span class="label label-primary">{{ $rowDetails->off }}</span>
            @endif
            @else
            {{ $dataTypeContent->{$row->field} }}
            @endif
            @elseif($row->type == 'rich_text_box')
            @include('voyager::multilingual.input-hidden-bread')
            <p>{{ strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') }}</p>
            @else
            @include('voyager::multilingual.input-hidden-bread')
            <p>{{ $dataTypeContent->{$row->field} }}</p>
            @endif
          </div><!-- panel-body -->
          @if(!$loop->last)
          <hr style="margin:0;">
          @endif
          @endforeach

        </div>
      </div> 
      <div id="users" class="tab-pane fade">

        <div class="panel-heading" style="border-bottom:0;">
          <h3 class="panel-title">Company Users</h3>
        </div>

        <div class="panel-body" style="padding-top:0;">
          <table id="company_user" class="table table-responsive table-stripped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th> 
                <th>Avatar</th> 
                <th></th>
              </tr>
            </thead> 
            <tbody id="result">
            </tbody>
          </table>
        </div>

      </div>

      <div id="attachments" class="tab-pane fade">

        <div class="panel-heading" style="border-bottom:0;">
          <h3 class="panel-title">Company Attachments</h3>
        </div>

        <div class="panel-body" style="padding-top:0;">
          <table id="company_user" class="table table-responsive table-stripped">
            <thead>
              <tr>
                <th>Id</th>
                <th>Subject</th> 
                <th>Attachment</th> 
                <th>Mime Type</th>
                <th>Action</th>
              </tr>
            </thead> 
            <tbody id="attachment-results">
            </tbody>
          </table>
        </div>

        <h4>Add New Attachments</h4>
        {!! Form::open(['method'=>'POST', 'files'=>'true', 'id'=>'attachmentForm', 'role'=>'form', 'data-toggle'=>'validator']) !!}
        {{ csrf_field() }}

        {{ Form::hidden('user_id',  Auth::id()  )}}
        {{ Form::hidden('company_id',  $id  )}}
        {{ Form::hidden('ajax',  'true'  )}}
        <div class="col-md-6 form-group">
          {{ Form::label('Subject', null, ['class' => 'control-label']) }}
          {{ Form::text('subject', '', array_merge(['class' => 'form-control','id'=>'subject', 'placeholder'=>'Subject','required'=>'required'])) }}
        </div>
        <div class=" col-md-6 form-group">
          {{ Form::label('Attachment', null, ['class' => 'control-label']) }}
          {{ Form::file('attachment',  ['class' => 'form-control', 'id'=>'attachment']) }}
        </div>
        <div class=" col-md-12 form-group">

          <button type="submit" class="btn btn-success">Save Attachments</button>
        </div>

        {!! Form::close() !!}
        <div class=" col-md-12 form-group">
          <p id="fileUploadError" class="text-danger hide"></p>

          <div class="progress">
            <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
          </div>

          <div class="msg"></div>
        </div>
      </div>

    </div>

  </div>

</div>
</div>

@stop

@section('javascript')
@if ($isModelTranslatable)
<script>
  $(document).ready(function () {
    $('.side-body').multilingual();


  });
</script>
<script src="{{ config('voyager.assets_path') }}/js/multilingual.js"></script>
@endif

<script>
  $(document).ready(function () { 
    loadUserByCompany('#result', {{$id}});
    loadattachmentsbycompany('#attachment-results', {{$id}});
    validateForm('#attachmentForm');
 
  });
  function loadUserByCompany( tbodyid,  company_id)
  {
    $.ajax({
      type: 'GET',
      url: '{{URL::to('/')}}/admin/usersbycompany/'+company_id,
      dataType: 'json',
      success: function (response) {
        var trHTML = '';
        for(var f=0;f<response.length;f++) {
          trHTML += '<tr><td><strong>' + response[f]['name']+'</strong></td><td>'+response[f]['email'] +'</td>';

          trHTML +='<td><img src="{{URL::to('/')}}/storage/'+response[f]['avatar'];



          trHTML +='" style="width:100px"></td><td>';
          @if (Voyager::can('read_users'))
          trHTML +=  ' <a href="{{URL::to('/')}}/admin/users/'+response[f]['id'] +'" class="btn-sm btn-warning pull-right "   ><i class="voyager-eye"></i> View </a>';
          @endif  
          trHTML +='</td> <tr> ';
        }
        $(tbodyid).html(trHTML); 
        $( ".spin-grid" ).removeClass( "fa-spin" );
      }
    });
  }

  function validateForm(id)
  {
   $(id).bootstrapValidator({
    framework: 'bootstrap',
    icon: {
      valid: 'glyphicon glyphicon-ok',
      invalid: 'glyphicon glyphicon-remove',
      validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
      attachment: {
        validators: {
          notEmpty: {
            message: 'Please select a file'
          },
          file: {
            extension: 'jpeg,jpg,png,pdf',
            type: 'application/pdf,image/jpeg,image/png',
                          maxSize: 12097152,   // 2048 * 1024 *10
                          message: 'The selected file is not valid'
                        }
                      }
                    }
                  },
                  onSuccess: uploadFiles
                });       
 }

 function loadattachmentsbycompany( tobdyid,  company_id)
 {
  $.ajax({
    type: 'GET',
    url: '{{URL::to('/')}}/admin/attachmentsbycompany/'+company_id,
    dataType: 'json',
    success: function (response) {
      var trHTML = '';
      for(var f=0;f<response.length;f++) {
        trHTML += '<tr><td><strong>' + response[f]['id']+'</strong></td><td>'+response[f]['subject']+'</td><td>'+response[f]['attachment']+'</td><td>'+response[f]['mime_type']+'</td><td>';


        @if (Voyager::can('read_company-attachments'))
        trHTML +=  ' <a  href="{{URL::to('/')}}/admin/generateattachments/'+response[f]['id'] +'"  class="btn-sm btn-info pull-right"><i class="voyager-download" ></i> Download </a>';
        @endif  
      }
      $(tobdyid).html(trHTML); 
      $( ".spin-grid" ).removeClass( "fa-spin" );
    }
  });

}


// Catch the form submit and upload the files
function uploadFiles(event, data)
{
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    // START A LOADING SPINNER HERE

$('.msg').text('Uploading in progress...');
// Serialize the form data
var formData = new FormData($('#attachmentForm')[0]);
formData.append('file', $('#attachment')[0].files[0]);
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$.ajax({
  url:  '{{URL::to('/')}}/admin/company-attachments',
  type: 'POST',
  data: formData,
  cache: false,
  dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data)
        {
          console.log(data);
          loadattachmentsbycompany('#attachment-results', {{$id}});
          $('#attachment').val('');
          $('#subject').val('');
           $('.msg').text('Upload Completed.'); 
          toastr[data['alert-type']](data['message']);
          $('#attachmentForm').bootstrapValidator('destroy');
          validateForm('#attachmentForm');
        },
        error: function(errorThrown)
        {
            // Handle errors here 
            toastr['error']('Error Occured.');
          },
          xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete = parseInt(percentComplete * 100);
                $('.myprogress').text(percentComplete + '%');
                $('.myprogress').css('width', percentComplete + '%');
              }
            }, false);
            return xhr;
          }, 
          mimeType:"multipart/form-data"


        });
}  
</script>
@stop

