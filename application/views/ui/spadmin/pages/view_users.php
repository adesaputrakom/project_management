<div class="content-wrapper" style="min-height: 1301.84px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Management Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                                    <h3 class="card-title">Management Users</h3>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-warning btn-block btn-flat" onclick="addData()"><i class="fa fa-plus"></i> Add Users </button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card filterTable">
                            
                            <?php  # option parameter
                            if($dataFilter){
                                if(count($dataFilter)>0){
                                    $html = "<div class='card-body' style='border-bottom:1px dashed blue'>
                                            <div class='row'>";

                                        for($i=0; $i < count($dataFilter); $i++){

                                            $id_filter = $dataFilter[$i]['id_filter'];
                                            $nama_filter = $dataFilter[$i]['nama_filter'];
                                            $options = $dataFilter[$i]['option_filter'];

                                            $html .= "<div class='col-3'>
                                                <select class='form-control select2' id='$id_filter'>
                                                    <option value=''> $nama_filter </option>";
                                                    
                                                    if($options){
                                                        foreach($options as $option){
                                                            $html .= "<option value='$option[id]'> $option[attr] </option>";
                                                        }
                                                    }

                                            $html .= "</select></div>";
                                        }

                                    $html .= '</div></div';

                                    echo $html;
                                }
                            }
                            ?>

                        </div>

                        <div class="card-body">
                            <table id="example2" class="table table-striped table-bordered nowrap" style="width:100%">

                                <thead>
                                    <tr>
                                        <?php 
                                        # looping thead (judul kolom)
                                        foreach ($coloumns as $thead=>$coloumn){
                                            echo "<th>$thead</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>


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
                    <input type="hidden" name="pengguna_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap <font class="text-danger">*</font></label>
                                <input type="text" class="form-control" name="nama" placeholder="masukkan nama lengkap">
                            </div>
                            <div class="form-group">
                                <label>Email <font class="text-danger">*</font></label>
                                <input type="email" class="form-control" name="email" placeholder="masukkan email">
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin <font class="text-danger">*</font></label>
                                <select class="form-control select2" name="jeniskelamin">
                                    <option value=""> -- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki"> Laki-laki </option>
                                    <option value="Perempuan"> Perempuan </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempatlahir" placeholder="masukkan tempat lahir">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="tanggallahir" data-target="#reservationdate" placeholder="pilih tanggal lahir">
                                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                        <div class="input-group-text bg-primary"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Pendidikan</label>
                                <select class="form-control select2" name="pendidikan">
                                    <option value=""> -- Pilih Pendidikan --</option>
                                    <option value="SD"> SD </option>
                                    <option value="SMP"> SMP </option>
                                    <option value="SMA"> SMA </option>
                                    <option value="D3"> D3 </option>
                                    <option value="S1"> S1 </option>
                                    <option value="S2"> S2 </option>
                                    <option value="S3"> S3 </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2" placeholder="masukkan Alamat"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username <font class="text-danger">*</font></label>
                                <input type="text" class="form-control" name="username" placeholder="masukkan username">
                            </div>
                            <div class="form-group">
                                <label>Foto <small class="text-info"> ( max : 2 mb )</small></label>
                                <input type="file" class="form-control" name="foto" placeholder="upload foto" onchange="checkSize(this)">
                            </div>
                            <div class="form-group">
                                <label>Company/Organisasi <font class="text-danger">*</font></label>
                                <input type="text" class="form-control" name="company" placeholder="masukkan company/perusahaan">
                            </div>
                            <div class="form-group">
                                <label>Departement/Bidang <font class="text-danger">*</font></label>
                                <select class="form-control select2" name="departement_id">
                                    <option value=""> -- Pilih Departement --</option>
                                    <?php if($departments){
                                        foreach($departments as $departemen){
                                            echo '<option value="'.encrypt($departemen->departement_id).'"> '.$departemen->departement.' </option>';
                                        }
                                    }?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Posisi</label>
                                <input type="text" class="form-control" name="posisi" placeholder="masukkan posisi/jabatan">
                            </div>
                            <div class="form-group">
                                <label>Agama</label>
                                <select class="form-control select2" name="agama">
                                    <option value=""> -- Pilih Agama --</option>
                                    <option value="Islam"> Islam </option>
                                    <option value="Kristen"> Kristen </option>
                                    <option value="Katholik"> Katholik </option>
                                    <option value="Konghucu"> Konghucu </option>
                                    <option value="Budha"> Budha </option>
                                    <option value="Hindu"> Hindu </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Set Administrator ? </label>
                                <div class="bootstrap-switch-container" style="width: 126px; margin-left: 0px;">
                                    <input type="checkbox" name="is_admin" value="No" checked="" data-bootstrap-switch="" data-off-color="success" data-off-text="Ya" data-on-text="Tidak" data-on-color="danger">
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

    document.addEventListener("DOMContentLoaded", function() {

        var table = $('#example2').DataTable({
            'processing': true,
            'serverSide': true,

            'serverMethod': 'post',
            //'searching': false, // Remove default Search Control
            'ajax': {
                'url': '<?= $url ?>',
                'data': function(data) {
                    
                    <?php  # getting value parameter
                    if($dataFilter){
                        if(count($dataFilter)>0){
                            for($i=0; $i < count($dataFilter); $i++){
                                $filter = $dataFilter[$i]['id_filter'];
                                echo "data.$filter = $('#$filter').val();";
                            }
                        }
                    }
                    ?>

                }
            },
            'columns': [
                <?php foreach ($coloumns as $thead=>$coloumn){ # value field table
                    echo "{ data : '$coloumn'},";
                }?>
            ],
            "scrollX": true
        });


        <?php  # getting event change parameter
        if($dataFilter){
            if(count($dataFilter)>0){
                for($i=0; $i < count($dataFilter); $i++){
                    $filter = $dataFilter[$i]['id_filter'];
                    echo "$('#$filter').change(function() {
                        table.draw();
                    });";
                }
            }
        }
        ?>
    });

    
    function resetForm(){
        // hide allert
        $('#message_error').hide();
        // reset form input normal
        $('#form')[0].reset();
        $('[name="pengguna_id"]').val('');
        $('[name="jeniskelamin"] :selected').removeAttr('selected');
        $('[name="pendidikan"] :selected').removeAttr('selected');
        $('[name="departement_id"] :selected').removeAttr('selected');
        $('[name="agama"] :selected').removeAttr('selected');
        // reset form select 
        $('.select2').select2({width : '100%'});
    }

    //reload table
    function reload_table(){
        $('#example2').DataTable().ajax.reload();
    }

    function addData(){
        resetForm();
        $(".modal-title").html('Add Data Users');
        $('#modal_add').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
        $("#modal_add").modal('show');
    }

    function save(){

        var id = $('[name="pengguna_id"]').val();
        var url = id ==  '' ? "<?=base_url().'spadmin/users/add'?>" : "<?=base_url().'spadmin/users/update'?>";

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
                    reload_table();

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
        $(".modal-title").html('Edit Data Users');
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/users/edit')?>/" + id,
            dataType : "JSON",
            success: function(data){
              $('#modal_add').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
              $('#modal_add').modal('show');
              $('[name="pengguna_id"]').val(data.pengguna_id);
              $('[name="nama"]').val(data.nama);
              $('[name="username"]').val(data.username);
              $('[name="jeniskelamin"] option[value="'+data.jeniskelamin+'"]').attr('selected', 'selected');
              $('[name="email"]').val(data.email);
              $('[name="username"]').val(data.username);
              $('[name="company"]').val(data.company);
              $('[name="departement_id"] option[value="'+data.departement_id+'"]').attr('selected', 'selected');
              $('[name="posisi"]').val(data.posisi);
              $('[name="tempatlahir"]').val(data.tempatlahir);

              if(data.tanggallahir){
                  $('[name="tanggallahir"]').val(data.tanggallahir);
              }

              $('[name="pendidikan"] option[value="'+data.pendidikan+'"]').attr('selected', 'selected');
              $('[name="agama"] option[value="'+data.agama+'"]').attr('selected', 'selected');
              $('[name="alamat"]').val(data.alamat);

              $('.select2').select2({width: "100%"});
            }
        });
        $.unblockUI();
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
                    url  : "<?php echo base_url('spadmin/users/hapus')?>/" + id,
                    dataType : "JSON",
                    success: function(data){
                        if(data.data){
                            reload_table();
                            Swal.fire({
                                title: "Berhasil",
                                text: "Data Berhasil dihapus",
                                type: "success",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500
                            });

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