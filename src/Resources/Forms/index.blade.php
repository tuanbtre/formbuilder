@extends('Admin.adminapp')
@section('styles')
   @parent
   <link rel="stylesheet" href="{{asset('vendor/csm/css/font-awesome.css')}}">
   <link rel="stylesheet" type="text/css" href="{{asset('vendor/csm/css/tabs.css')}}"/> 
@endsection
@section('content')
<div class="page animsition" style="animation-duration: 800ms; opacity: 1;">
   <div class="col-md-12 col-xs-12">
      <div class="row">
         <div class="col-md-6">
            <h1 style="background:inherit">Danh sách Quản lý Form</h1>   
         </div>
         <div class="col-md-6">
            <p style="display: flex; justify-content: right; align-items: center;padding-top:15px;padding-right:15px">Ngôn ngữ &nbsp; <select class="form-control" style="width:18%" onchange = "DDLLanguageChange(this.value)">
               @foreach($language as $item)
                  <option value="{!! $item['id'] !!}" {!! $item['id']==$current_language? "selected" : "" !!}>{!! $item['lang_name'] !!}</option>
               @endforeach
            </select></p>
         </div>         
      </div>      
   </div>
   <div class="page-content padding-30 container-fluid">
      <div class="col-xs-12 padding-0">
         <div class="col-md-1 col-sm-1 col-xs-12 padding-0">
            <a class="button-themmoi btn btn-info btn-lg" onclick="SetNew()" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>Thêm mới</a>
         </div>
         <div class="col-md-4 col-sm-4 col-xs-12 padding-left-0">
            <div class="slinput">
               <i class="fa fa-search left-icon"></i> 
               <input id="searchstr"name="searchstr" placeholder="Search here">
               <a href="{!!route('admin.form.index',['l'=>$current_language])!!}"><i class="fa fa-refresh right-icon-re"></i></a>
               <i class="fa fa-caret-down right-icon-select"></i> 
               <a href="javascript:void(0);" onclick="if(searchstr.value)location.href='{!!route('admin.form.index',['l'=>$current_language])!!}&search='+searchstr.value;else searchstr.placeholder='Nhập nội dung cần tìm'" class="right-icon-a">Tìm kiếm</a>
            </div>
         </div>
         <div class="pull-right number-row">{!!$list->firstItem()!!} - {!!$list->lastItem()!!} của {!!$list->total()!!} <a href="#" class="button-next"><i class="fa fa-angle-right"></i></a>Trang {!!$list->currentPage()!!}</div>
      </div>
   @if($list->count())
   <div class="col-xs-12 padding-0 margin-top-30">
      <fieldset >
         <div class="scrollable">
            <table class="table-user text-center">
               <thead>
                  <tr>
                     <th>STT</th>
                     <th>Tiêu đề</th> 
                     <th>Thời gian bắt đầu</th>
                     <th>Thời gian kết thúc</th>
                     <th>Trường</th>
                     <th>Thứ tự</th>
                     <th>Hoạt động</th>
                     <th>Tùy chỉnh</th>                      
                  </tr>
               </thead>
               <tbody>
                  @foreach($list as $key=>$item)
                  <tr>                              
                     <td>{!!$loop->iteration+(($list->currentPage()-1)*15)!!}</td>
                     <td>{!!$item->title!!}</td>
                     <td>{!!$item->start_time!!}</td>
					 <td>{!!$item->end_time!!}</td>
                     <td>
						@foreach ($item->fields as $field)
                            {{ $field['name'] }} ({{ $field['type'] }}, max: {{ $field['max_length'] }})<br>
                        @endforeach
					 </td>                     
                     <td>{!!$item->priority!!}</td>
                     <td><input type="checkbox" name="isactive" {!!$item->isactive==1? 'checked' : ''!!} disabled></td>
                     <td id="row_{{$item['id']}}" data-title="{{$item['title']}}" data-isactive="{{$item['isactive']}}" data-start_time="{!! $item['start_time'] !!}" data-end_time="{{$item['end_time']}}"  data-fields="{{$item['fields']}}" data-priority="{{$item['priority']}}">
                       <i class="fa fa-pencil" onclick="Set({{$item['id']}})"></i>
                       <i class="fa fa-times" onclick="SetDeleteMode({{$item['id']}})"></i>
                     </td>                  
                  </tr>
                  @endforeach
               </tbody>
            </table>               
         </div>                    
      </fieldset>
   </div>
   @endif
   @if($list->lastPage()>1)
      {!!$list->appends(request()->except('page'))->links('Admin.pagination.index')!!}
   @endif       
  </div>  
</div>
<div id="myModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
      <div class="modal-body areaform">
         <section class="bg-white">
          <form name="MainForm" class="cd-form floating-labels" method="post" action="{!!route('admin.form.index')!!}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="Id" name="Id" value="0">
            <input type="Hidden" id="deleteMode" name="deleteMode" value="0">
            <input type="hidden" id="l" name="l" value="{!!$current_language!!}">
            <legend>Quản lý Form</legend>
            <fieldset>
               <div class="col-md-12">
                  <label class="cd-label" for="cd-name">Tiêu đề</label>
                  <input required id="title" name="title" type="text">
               </div>
               <div class="col-md-5">
                  <label class="cd-label" for="start_time">Thời gian bắt đầu</label>
                  <input id="start_time" name="start_time" type="text">
               </div>
			   <div class="col-md-5">
                  <label class="cd-label" for="end_time">Thời gian kết thúc</label>
                  <input id="end_time" name="end_time" type="text">
               </div>
               <div class="col-md-12">
					<label class="cd-label">Thêm trường</label>
					<div id="fields">
						<div class="field mb-2 flex space-x-2 items-center">
							<input type="text" name="fields[0][name]" class="w-1/3 border px-3 py-2" placeholder="Tên trường" required>
							<select name="fields[0][type]" class="w-1/3 border px-3 py-2" required>
								<option value="text">Text</option>
								<option value="number">Number</option>
								<option value="email">Email</option>
								<option value="textarea">Textarea</option>
							</select>
							<input type="number" name="fields[0][max_length]" class="w-1/3 border px-3 py-2" placeholder="Giới hạn ký tự" min="1" max="1000" required>
							<button type="button" class="remove-field bg-red-500 text-white px-2 py-1 rounded" disabled>Xóa</button>
						</div>
					</div>
					<button type="button" id="addField" class="bg-green-500 text-white px-4 py-2 rounded">Thêm trường</button>
			   </div>
               <div class="col-md-12">
                <label class="cd-label" for="priority">Thứ tự</label>
                <input type="text" id="priority" name="priority">
               </div>
               <div class="col-md-6">
                <input class="tran-check" type="checkbox" id="isactive" name="isactive" value="1" checked>
                <label class="cd-label" for="isactive">Hoạt động</label> 
               </div>             
               <div class="col-md-12 text-center">
                  <input type="submit" class="btn b-save" value="Lưu thông tin">
                  <input type="reset" class="btn b-normal" value="Làm lại">
               </div>
            </fieldset>
          </form>
         </section>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" onclick="SetNew()" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('javascript')
   @parent
   <script src="{{asset('vendor/csm/js/admintool.js')}}"></script>
   <script src="{{asset('vendor/csm/js/number.js')}}"></script>
   <script type="text/javascript">
   function Set(_id) {
      $('#alert').remove();
      var id_row = 'row_' + _id;
      $('#Id').val(_id);
      SetRecordTextBox('title', id_row, 'data-title', '');
      SetRecordTextBox('start_time', id_row, 'data-start_time', '');
      SetRecordTextBox('end_time', id_row, 'data-end_time', '');
      SetRecordTextBox('meta_description', id_row, 'data-meta-description', '');
      SetRecordTextBox('priority', id_row, 'data-priority', '');
      SetRecordCheckBox('isactive', id_row, 'data-isactive', '');
      $('#myModal').modal('show');
   }
   function SetNew() {
      $('#Id').val('0');
      document.forms["MainForm"].reset();
   }
   function SetDeleteMode(_id) {
      if(confirm('Bạn có thật sự muốn xóa phần tử này không?'))
      {
         $('#deleteMode').val('1');
         $('#Id').val(_id);
         document.forms["MainForm"].submit();
      }
   }
   function DDLLanguageChange(_lang) {
      document.location.href = '{{url('admin/form')}}?l=' + _lang;
   }    
   $("#delivery_book").datepicker({dateFormat: "dd-mm-yy",minDate:"0"});
   $("#delivery_date").datepicker({dateFormat: "dd-mm-yy",minDate:"0"});
   let fieldIndex = 1;

        // Thêm trường mới
        $('#addField').click(function() {
            $('#fields').append(`
                <div class="field mb-2 flex space-x-2 items-center">
                    <input type="text" name="fields[${fieldIndex}][name]" class="w-1/3 border px-3 py-2" placeholder="Tên trường" required>
                    <select name="fields[${fieldIndex}][type]" class="w-1/3 border px-3 py-2" required>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="textarea">Textarea</option>
                    </select>
                    <input type="number" name="fields[${fieldIndex}][max_length]" class="w-1/3 border px-3 py-2" placeholder="Giới hạn ký tự" min="1" max="1000" required>
                    <button type="button" class="remove-field bg-red-500 text-white px-2 py-1 rounded">Xóa</button>
                </div>
            `);
            fieldIndex++;
            updateRemoveButtons();
        });

        // Xóa trường
        $(document).on('click', '.remove-field', function() {
            $(this).closest('.field').remove();
            updateRemoveButtons();
        });

        // Cập nhật trạng thái nút Xóa (vô hiệu hóa nút Xóa của trường đầu tiên)
        function updateRemoveButtons() {
            $('.remove-field').prop('disabled', false); // Bật tất cả nút Xóa
            $('#fields .field:first .remove-field').prop('disabled', true); // Vô hiệu hóa nút Xóa của trường đầu tiên
        }
  </script>
@endsection