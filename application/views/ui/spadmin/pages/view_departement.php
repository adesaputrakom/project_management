<div class="content-wrapper" style="min-height: 1301.84px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Management Departement</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Departement</li>
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
                                <div class="col-9">
                                    <h3 class="card-title">Management Departement</h3>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-warning btn-block btn-flat" onclick="addData()"><i class="fa fa-plus"></i> Add Departement </button>
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
                                        $x=0;
                                        foreach ($coloumns as $thead=>$coloumn){
                                            $size[$x] = $size[$x] ? "width='$size[$x]px'" : '';

                                            echo "<th $size[$x] >$thead</th>";
                                            $x++;

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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Title Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- message error -->
                <div id="message_error"></div>

                <form action="#" id="form">
                    <!-- primary key -->
                    <input type="hidden" name="departement_id">

                    <div class="form-group">
                        <div class="row align-items-center">
                        <label class="col-sm-2">Departement :</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="departement" placeholder="nama departement">
                        </div>
                        </div>
                    </div> 
                </form>
                <!-- End Form -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Save</button>
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

    
    // resetForm
    function resetForm(){
        // hide allert
        $('#message_error').hide();
        // reset form input normal
        $('#form')[0].reset();
        $('[name="departement_id"]').val('');

        $.blockUI({
          message: '<img src="<?= base_url() ?>assets/dist/gif/ajax-loader.gif" width="50"> Please Wait ...',
          css: {
            border: 'none',
            backgroundColor: 'transparent',
            color: '#fff'
          }
        });
    }

    //reload table
    function reload_table(){
        $('#example2').DataTable().ajax.reload();
    }

    function addData(){
        resetForm();
        $(".modal-title").html('Add Data Departement');
        $("#modal_add").modal('show');
        $.unblockUI();
    }

    function save(){

        var id = $('[name="departement_id"]').val();
        var url = id ==  '' ? "<?=base_url().'spadmin/departement/add'?>" : "<?=base_url().'spadmin/departement/update'?>";

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
        $(".modal-title").html('Edit Data Departement');
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/departement/edit')?>/" + id,
            dataType : "JSON",
            success: function(data){
              $('#modal_add').modal('show');
              $('[name="departement_id"]').val(data.departement_id);
              $('[name="departement"]').val(data.departement);
            }
        });
        $.unblockUI();
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
                    url  : "<?php echo base_url('spadmin/departement/hapus')?>/" + id,
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