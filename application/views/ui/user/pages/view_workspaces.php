
  <style>
    .color-palette {
      height: 35px;
      line-height: 35px;
      text-align: right;
      padding-right: .75rem;
    }

    .color-palette.disabled {
      text-align: center;
      padding-right: 0;
      display: block;
    }

    .color-palette-set {
      margin-bottom: 15px;
    }

    .color-palette span {
      color: #000000;
      font-size: 12px;
    }
    .color-palette.disabled span {
      display: block;
      text-align: left;
      padding-left: .75rem;
    }

    .color-palette-box h4 {
      position: absolute;
      left: 1.25rem;
      margin-top: .75rem;
      color: rgba(255, 255, 255, 0.8);
      font-size: 12px;
      display: block;
      z-index: 7;
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <div class="container-fluid">
              <div class="row mb-2">
                  <div class="col-sm-6">
                      <h1>Workspaces</h1>
                  </div>
                  <div class="col-sm-6">
                      <ol class="breadcrumb float-sm-right">
                          <li class="breadcrumb-item"><a href="#">Home</a></li>
                          <li class="breadcrumb-item active">Workspaces</li>
                      </ol>
                  </div>
              </div>
          </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <h3 class="card-title">Management Workspaces</h3>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-warning btn-block btn-flat" onclick="addData()"><i class="fa fa-plus"></i> Add Workspaces </button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="row">

                                <?php 
                                    if($workspaces){
                                        foreach ($workspaces as $w){
                                            $w->departement = $w->departement ? $w->departement : 'Umum';
                                            echo '<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                                    <div class="card bg-light d-flex flex-fill">
                                                        <div class="card-header text-muted border-bottom-0">
                                                            '.$w->departement.'
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <div class="row">
                                                                <div class="col-7">
                                                                    <h2 class="lead"><b>'.$w->nm_workspace.'</b></h2>
                                                                    <hr>
                                                                    <p class="text-muted text-sm"><b>Description : </b> '.$w->description.'</p>
                                                                    <ul class="ml-4 mb-0 fa-ul text-muted">
                                                                        <li class="small mb-1"><span class="fa-li"><i class="fas fa-lg fa-users text-success"></i></span> '.$w->totalmember.' Member</li>
                                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-th text-primary"></i></span> '.$w->totalboard.' Board/Tahapan</li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-5 text-center">
                                                                    <!-- thumbnail workspaces -->
                                                                    <img src="'.base_url().'assets/uploads/file/'.$w->thumbnail.'" alt="workspaces-avatar" class="img-circle img-fluid">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- card footer color dinamis -->
                                                        <div class="card-footer" style="background-color: '.$w->color.';">
                                                            <div class="text-right">
                                                                <a href="'.base_url().'user/kanbanboard/get/'.encrypt($w->workspace_id).'" class="btn btn-sm btn-default text-primary mr-1" title="view">
                                                                    <i class="fas fa-eye"></i> View Kanban Board
                                                                </a>';

                                                            if($myWorkspace){
                                                                if($myWorkspace[$w->workspace_id]){
                                                                    echo'<a href="javascript:edit(\''.encrypt($w->workspace_id).'\');" class="btn btn-sm btn-default text-primary" title="edit">
                                                                            <i class="fas fa-pen"></i> Edit
                                                                        </a>
                                                                        <a href="javascript:hapus(\''.encrypt($w->workspace_id).'\');" class="btn btn-sm btn-default text-danger" title="delete">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>';
                                                                }
                                                            }

                                                        echo'</div>
                                                        </div>
                                                    </div>
                                                </div>';
                                                }
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
      </section>
      <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="modal_add" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Title Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 50px;">

                <!-- message error -->
                <div id="message_error"></div>

                <form action="#" id="form">
                    <input type="hidden" name="workspace_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Workspaces <font class="text-danger">*</font></label>
                                <input type="text" class="form-control" name="workspace" placeholder="masukkan nama lengkap">
                            </div>
                            <div class="form-group">
                                <label>Departement/Team <font class="text-danger">*</font></label>
                                <select class="form-control select2" name="departement_id" onchange="getTeam()">
                                    <option value=""> Umum </option>
                                    <?php if($departements){
                                        foreach($departements as $departemen){
                                            echo '<option value="'.encrypt($departemen->departement_id).'"> '.$departemen->departement.' </option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Color <font class="text-danger">*</font></label>
                                <div class="input-group my-colorpicker2">
                                    <input type="text" class="form-control" name="color" placeholder="klik untuk pilih warna">

                                    <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-square"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Logo/Thumbnail <small>( max : 2 Mb)</small></label>
                                <input type="file" class="form-control" name="file" accept="image/*" onchange="checkSize(this)">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Request By <font class="text-danger">*</font></label>
                                <select class="form-control select2" id="createdby" name="createdby">
                                    <option value=""> -- Request By -- </option>
                                    <?php if($users){
                                        foreach($users as $user){
                                            echo '<option value="'.encrypt($user->pengguna_id).'"> <b>'.$user->nama.'</b> | '.$user->email. '</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="2" placeholder="deskripsi"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Board <font class="text-danger">*</font> <small>(pilih sesuai urutan)</small></label>
                                <select class="form-control select2" name="board_id[]" data-placeholder="Pilih Board" multiple>
                                    <option value=""> -- Pilih Board -- </option>
                                    <?php if($boards){
                                        foreach($boards as $board){
                                            echo '<option value="'.encrypt($board->board_id).'"> '.$board->board.' </option>';
                                        }
                                    }?>
                                </select>
                            </div>

                            <hr>
                            <b>Add Member : <font class="text-danger">*</font> <small>(tambah anggota)</small></label></b>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <select class="form-control select2" id="pengguna">
                                            <option value=""> -- Pilih Pegawai -- </option>
                                            <?php if($users){
                                                foreach($users as $user){
                                                    echo '<option value="'.encrypt($user->pengguna_id).'"> <b>'.$user->nama.'</b> | '.$user->email. '</option>';
                                                }
                                            }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-primary btn-block" onclick="addMember();"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="card card-default color-palette-box">
                                <div class="card-header bg-info">
                                    <h3 class="card-title">
                                    <i class="fas fa-users"></i>
                                        Team Member
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="callout callout-info">
                                        <div class="row teamMember">
                                            <!-- list team member -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </form>
                <!-- End Form -->

                <hr>
                <b>Note :</b>
                Tanda (<font class="text-danger">*</font>) artinya wajib di isi !
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_simpan" onclick="save()">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

  <script type="text/javascript">

        // resetForm
        function resetForm(){
            // hide allert
            $('#message_error').hide();
            // reset form input normal
            $('#form')[0].reset();
            $('[name="workspace_id"]').val('');
            $('.my-colorpicker2 .fa-square').css('color', '#444444');
            $('.teamMember').empty();
            $('[name="departement_id"] :selected').removeAttr('selected');
            $('[name="createdby"] :selected').removeAttr('selected');
            $('[name="board_id[]"]').removeAttr('selected');
            // reset form select 
            $('.select2').select2({width : '100%'});
            loading();
        }

        function loading(){            
            $('.modal-content').block({
                message: '<img src="<?= base_url() ?>assets/dist/gif/ajax-loader.gif" width="50"> Please Wait ...',
                css: {
                    border: 'none',
                    backgroundColor: 'transparent',
                    color: '#fff'
                }
            });
        }

        function checkSize(t) {
            if(typeof t.files[0] !== 'undefined'){
                size = t.files[0].size/1024;
                ext = t.files[0].type;
                // console.log(size, ext);
                // return false;
                if(ext == 'image/png' || ext == 'image/jpeg' || ext == 'image/jpg'){
                    if(size > 2300){
                        $(t).val('');;

                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning !',
                            text: 'Ukuran file yang diupload melebihi dari 2 Mb !'
                        });
                    }
                }else{
                    $(t).val('');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning !',
                        text: 'File yang diupload bukan JPEG,JPG,PNG !'
                    });
                }    
            }
        }

        function addData(){
            resetForm();
            $('.modal-title').html('Add Workspaces');
            $('#modal_add').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
            $('#modal_add').modal('show');
            $('.modal-content').unblock(); 
        }

        function addMember(){
            loading();
            var id = $('#pengguna').val();
            if(id){
                var dataMember = $('.member').length;
                if(dataMember > 0){
                    var pengguna_id = document.getElementsByName('pengguna_id[]');
                    var dtPengguna = [];
                    //nilai revisi penggunaid dalam array
                    for (var i = 0; i < pengguna_id.length; i++) {
                        dtPengguna.push(pengguna_id[i].value); 
                    }

                    // pengguna yang sudah terdaftar member tidak dapat dipilih lagi
                    if (dtPengguna.includes(id)){

                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian ..',
                            text: 'Pegawai ini sudah dipilih !!',
                        });
                    }else{
                        getdataMember(id);
                    }
                }else{
                    getdataMember(id);
                }
            }else{
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian ..',
                    text: 'Pilih pegawai terlebih dahulu !',
                });
            }
            $('.modal-content').unblock();
        }

        function getdataMember(id){
            // cek data perusahaan dan append ke team member
            $.ajax({
                type : "GET",
                url  : "<?php echo base_url('user/workspaces/addMember')?>/" + id,
                dataType : "JSON",
                success: function(data){
                    if(data.data){
                        var html = `<div class="col-md-3 member" title="${data.nama}">
                                        <input type="hidden" name="pengguna_id[]" value="${data.pengguna_id}">
                                        <h4 class="text-center">${data.nama}</h4>
                                        <div class="color-palette-set">
                                            <div class="bg-info color-palette">
                                                <span>
                                                    <a href="javascript:deleteAnggota(this);" title="delete" class="btn btn-sm text-white person-member" style="width: 20%;">
                                                        <i class="fas fa-trash"></i> 
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>`;

                        //append html
                        $('.teamMember').append(html);
                        $('#pengguna').prop('selectedIndex',0);
                        $('.select2').select2({width : '100%'});
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Opps..',
                            text: 'Pegawai tidak ditemukan !!',
                        });
                    }
                }
            });
        }

        function getTeam(){
            loading();
            var departementid = $('[name="departement_id"]').val();
            var namaDepartement = $('[name="departement_id"] option:selected').text();
            if(departementid){
                // cek data pegawai departement dan append ke team member
                $.ajax({
                    type : "GET",
                    url  : "<?php echo base_url('user/workspaces/addTeamDepartement')?>/" + departementid,
                    dataType : "JSON",
                    success: function(data){
                        if(data.data){
                            for(var i = 0; i < data.pengguna_id.length; i++){
                                var html = `<div class="col-md-3 member" title="${data.nama[i]}">
                                                <input type="hidden" name="pengguna_id[]" value="${data.pengguna_id[i]}">
                                                <h4 class="text-center">${data.nama[i]}</h4>
                                                <div class="color-palette-set">
                                                    <div class="bg-info color-palette">
                                                        <span>
                                                            <a href="javascript:deleteAnggota(this);" title="delete" class="btn btn-sm text-white person-member" style="width: 20%;">
                                                                <i class="fas fa-trash"></i> 
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>`;
                            }

                            //append html
                            $('.teamMember').empty().append(html);
                            
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps..',
                                text: 'Pegawai departement '+namaDepartement+' ini tidak ditemukan !!',
                            });
                            $('[name="departement_id"]').prop('selectedIndex',0);
                            $('.select2').select2({width : '100%'});

                            //reset team member
                            $('.teamMember').empty();
                        }
                    }
                });
            }else{
                //reset team member
                $('.teamMember').empty();
            }
            $('.modal-content').unblock(); 
        }

        function deleteAnggota(key){
            var x = $('.person-member').index(key);
            $('.member').eq(x).remove();
        }

        function save(){

            var id = $('[name="workspace_id"]').val();
            var url = id ==  '' ? "<?=base_url().'user/workspaces/add'?>" : "<?=base_url().'user/workspaces/update'?>";

            $.ajax({
                url : url,
                type: "POST",
                data:new FormData($('#form')[0]), //this is formData
                processData:false,
                contentType:false,
                cache:false,
                async:false,
                dataType: "JSON",
                success: function(data)
                {
                    if(data.status == 'success') 
                    {
                        $('#modal_add').modal('hide');
                        toastr.success('Success', data.message, {timeOut: 1200});
                        window.setTimeout(function(){location.reload()},800);

                    } else if (data.status == 'invalid'){

                        html = `<div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-info"></i> Alert !</h5>
                                    ${data.message}
                                </div>`;

                        $('#message_error').empty().html(html);
                        $('#message_error').show();
                        
                    } else {
                        $('#modal_add').modal('hide');
                        toastr.error('Gagal', data.message, {timeOut: 1200});
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    toastr.error('Gagal', data.message, {timeOut: 2000});
                    $('#btnSave').attr('disabled',false); //set button enable 
                }
            });
        }

        function edit(id){
            resetForm();
            $(".modal-title").html('Edit Workspaces');
            $.ajax({
                type : "GET",
                url  : "<?php echo base_url('user/workspaces/edit')?>/" + id,
                dataType : "JSON",
                success: function(data){
                    $('#modal_add').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
                    $('#modal_add').modal('show');

                    $('[name="workspace_id"]').val(data.workspace_id);
                    $('[name="workspace"]').val(data.workspace);
                    $('[name="color"]').val(data.color);
                    $('.my-colorpicker2 .fa-square').css('color', data.color);
                    $('[name="departement_id"] option[value="'+data.departement_id+'"]').attr('selected', 'selected');
                    $('[name="createdby"] option[value="'+data.createdby+'"]').attr('selected', 'selected');
                    $('[name="deskripsi"]').val(data.description);

                    // board 
                    $('[name="board_id[]"]').val(data.board_id);

                    // member
                    var html = '';
                    for(var i=0; i<data.pengguna_id.length; i++){

                            html += `<div class="col-md-3 member" title="${data.nama[i]}">
                                        <input type="hidden" name="pengguna_id[]" value="${data.pengguna_id[i]}">
                                        <h4 class="text-center">${data.nama[i]}</h4>
                                        <div class="color-palette-set">
                                            <div class="bg-info color-palette">
                                                <span>
                                                    <a href="javascript:deleteAnggota(this);" title="delete" class="btn btn-sm text-white person-member" style="width: 20%;">
                                                        <i class="fas fa-trash"></i> 
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>`;

                    }
                                
                    //append html
                    $('.teamMember').append(html);
                    $('.select2').select2({width: "100%"});
                }
            });
            $('.modal-content').unblock(); 
        }

        function hapus(id){

            Swal.fire({
                title: 'Apakah anda yakin ?',
                text: "Apakah anda yakin menghapus data ini ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Yakin !'
                }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type : "GET",
                        url  : "<?php echo base_url('user/workspaces/hapus')?>/" + id,
                        dataType : "JSON",
                        success: function(data){
                            if(data.data){
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Data Berhasil dihapus",
                                    type: "success",
                                    icon: "success",
                                    showConfirmButton: true,
                                }).then(function(){location.reload()});
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                });
                            }
                        }
                    });
                }
                });
            }
  </script>