<style type="text/css">
    .user-block img {
        float: left;
        height: 30px;
        width: 30px;
    }

    .modal.fade:not(.in).right .modal-dialog {
        -webkit-transform: translate3d(10%, 0, 0);
        transform: translate3d(5%, 0, 0);
    }
    .delhover{
        color: white;
    }
    a.delhover:hover {
        color: red;
    }
    .tools-info{
        color : #007bff;
    }
    .tools-danger{
        color : red;
    }

    .opacity{
        opacity: 0.05;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper kanban">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <input type="hidden" name="workspace_id" value="<?=encrypt($dataworkspace->workspace_id);?>">
                    <h5 class="text-primary"><?=$dataworkspace->nm_workspace;?></h5>
                    <?php if($unitkerjas){
                        echo'<select class="form-control select2" id="filterUnitkerja" onchange="filter_kanbanboard()">
                        <option value="">Filter Unit Kerja</option>';
                        foreach($unitkerjas as $list){
                                echo'<option value="'.encrypt($list->unitkerja_id).'">'.$list->unitkerja.'</option>';
                        }
                        echo'</select>';
                    }?>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">
                            <button type="button" class="btn btn-primary btn-block btn-flat" onclick="addCard()"><i class="fa fa-plus"></i> Add Card </button>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-3">
        <div class="container-fluid h-100" id="papanboard">

            <!-- tampilkan data board -->
            <?php if ($databoard){
                $output = '';
                foreach($databoard as $board){
                    $board_id = encrypt($board->board_id);
                    $output .= '
                    <div class="card card-row board">
                        <div class="card-header" style="background-color:'.$board->color.'">
                            <input type="hidden" name="board_id[]" value="'.$board_id.'">
                            <h1 class="card-title text-white text-bold">
                                '.$board->board.'
                            </h1>
                        </div>
                        <div class="card-body status" id="idboard'.$board_id.'">
                            <!-- card body -->';

                            if($datacard){
                                if($datacard[$board_id]){
                                    foreach($datacard[$board_id] as $card){
                                        $output .= '
                                        <div class="card card-light card-outline todo" draggable="true" id="'.encrypt($card->card_id).'" onclick="showCard(\'' . encrypt($card->card_id) . '\');">
                                            <div class="card-header">
                                                <h5 class="card-title text-md">'.$card->nama_card.' ('.$card->unitkerja.')</h5>
                                                <div class="card-tools pr-2 pt-2">';

                                                if($dataLabel[encrypt($card->card_id)]){
                                                    foreach ($dataLabel[encrypt($card->card_id)] as $label){
                                                        $output .= '
                                                        <span class="badge" style="background-color:'.$label->color.';color:white;">'.$label->label.'</span>';
                                                    }
                                                }

                                        $output .= '
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <a class="text-sm user-block">';

                                                if($dataAssignment[encrypt($card->card_id)]){
                                                    foreach($dataAssignment[encrypt($card->card_id)] as $assigment){
                                                        $output .= '
                                                        <img alt="Avatar" class="direct-chat-img" src="'.base_url().'assets/uploads/foto/'.$assigment->foto.'" draggable="false" ondragstart="return false;" title="'.$assigment->nama.'">';
                                                    }
                                                }

                                        # jumlah comment
                                        $jumlah = $dataComment[encrypt($card->card_id)];

                                        if($card->duedate){
                                            $duedateCard = getDuedate($card->duedate);
                                            $card->duedate = date('d/M/Y H:i', strtotime($card->duedate));
                                            $class = $duedateCard == 'Has Expired' ? 'text-danger' : 'text-info';
                                            $carddeadline = '<small class="badge badge-default '.$class.'" title="'.$card->duedate.'"><i class="far fa-calendar"></i> '.$duedateCard.' |</small>';
                                        }else{
                                            $carddeadline = '';
                                        }

                                        $output .= '
                                                </a>
                                                <span class="float-right">
                                                    <a class="text-gray text-sm">
                                                        '.$carddeadline.'
                                                        <i class="far fa-comments mr-1"></i> Comments ('.$jumlah.')
                                                    </a>
                                                </span>
                                            </div>
                                        </div>';
                                    }
                                }
                            }

                        $output .= '
                        </div>
                    </div>';
                }

                echo $output;
            }?>

        </div>
    </section>
</div>

<!-- form modal add card -->
<div class="modal fade" id="modal_addcard" role="dialog" aria-labelledby="modal_add" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Card</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- message error -->
                <div id="message_error"></div>

                <form action="#" id="form">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Nama Card :</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="card" placeholder="nama card">
                            </div>
                            <label class="col-sm-2">Unit Kerja :</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="unitkerja_id">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <?php if($unitkerjas){
                                        foreach($unitkerjas as $unitkerja){
                                            echo '<option value="'.encrypt($unitkerja->unitkerja_id).'"> '.$unitkerja->unitkerja.' </option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- End Form -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="saveCard()">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- form modal card -->
<div class="modal fade right" id="modal_card" role="dialog" aria-labelledby="modal_add" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Card</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 50px;">
                <div class="row">
                    <input type="hidden" name="card_id">
                    <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                        <div class="row">
                            <div class="card col-md-12">
                                <div class="card-header">
                                    <h3 class="card-title">
                                    <i class="ion ion-clipboard mr-1"></i>
                                        List Pekerjaan
                                    </h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <ul class="todo-list" data-widget="todo-list">
                                        <!-- list pekerjaan -->
                                    </ul>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <button type="button" class="btn btn-primary float-right" onclick="addItem();"><i class="fas fa-plus"></i> Add item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                        <h3 class="text-primary namacard"></h3> <!--namacard -->

                        <div class="form-group inputnamacard" style="display: none;">
                            <label style="font-weight : unset"> Nama Card : </label>
                            <textarea class="form-control text-muted" rows="2" name="cardname" placeholder="Masukkan Nama Card" onfocusout="updateNamacard()"></textarea>
                        </div>

                        <div class="form-group">
                            <label style="font-weight : unset"> Deskripsi : </label>
                            <textarea class="form-control text-muted" rows="5" name="deskripsi" placeholder="Deskripsi" onfocusout="saveDeskripsi();"></textarea> <!-- deskripsi -->
                        </div>

                        <div class="form-group">
                            <label style="font-weight : unset"> Duedate : </label>
                            <div class="input-group datetime" id="reservationdatetime" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime" name="duedate" placeholder="duedate" onfocusout="saveDuedate();">
                                <div class="input-group-append" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="post clearfix"></div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm">Join Team</button>
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></button>
                            <div class="dropdown-menu" role="menu" x-placement="bottom-start">
                                <a class="dropdown-item" href="javascript:join()">Join to contribute</a>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm">Label Card</button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></button>
                            <div class="dropdown-menu" role="menu" x-placement="bottom-start">
                                <?php if($labels){
                                    foreach($labels as $label){
                                        echo'<a class="dropdown-item" style="color:'.$label->color.'" href="javascript:setLabel(\'' . encrypt($label->label_id) . '\')"> '.$label->label.'</a>';
                                    }
                                }?>
                            </div>
                        </div>
                        <div class="text-muted mt-3">
                            <p class="text-sm"> Collaborator :
                                <b class="d-block assignmentname"></b> <!-- nama assignment -->
                            </p>
                        </div>
                        <div class="text-muted mt-3">
                            <p class="labelcards"></p> <!-- label card -->
                        </div>
                        <div class="post clearfix"></div>

                        <div class="row">
                            <h5 class="mt-1 text-muted col-md-5">Project files </h5>
                            <small class="ml-2 text-primary"> (docx,xlsx,pptx,jpg,jpeg,png,png,pdf,rar,zip,txt) <br> Ukuran max : 5 MB</small>
                            <div class="col-md-12">
                                <input type="file" class="form-control" name="fileupload" id="fileupload">
                            </div>
                        </div>
                        <ul class="list-unstyled mt-2 listfile">
                            <!-- list file project -->
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Komentar -->
                        <div class="card direct-chat direct-chat-primary">
                            <div class="card-header">
                                <h3 class="card-title">Comments</h3>

                                <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="direct-chat-messages _comment">
                                    <!-- isi list komentar -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="input-group">
                                    <input type="text" name="chatbox" placeholder="Type Comment ..." class="form-control">
                                    <span class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="sending">Send</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card direct-chat direct-chat-primary collapsed-card">
                            <div class="card-header bg-gradient-primary">
                                <h3 class="card-title">History Card</h3>

                                <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                </div>
                            </div>
                            <div class="card-body" id="historyCard">
                                <div class="direct-chat-messages _history">
                                    <!-- list history card -->             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-outline-danger col-sm-2" onclick="deleteCard();">Delete Card</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- form modal add item -->
<div class="modal fade" id="modal_item" role="dialog" aria-labelledby="modal_item" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Item</h4>
                <button type="button" class="close" aria-label="Close" onclick="closeItem()">
                <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- message error -->
                <div id="message_item"></div>

                <form action="#" id="form_item">
                    <div class="form-group row">
                        <input type="hidden" name="carddetail_id">
                        <input type="text" class="form-control" name="list" placeholder="Nama item pekerjaan">
                    </div>
                    <div class="form-group row">
                        <select class="form-control custom-select col-sm-6" name="assignment_list">
                            <!-- select assignmentItem -->
                        </select>

                        <div class="input-group datetime col-sm-3" id="reservationdatetime2" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime2" name="startdate" placeholder="start date">
                            <div class="input-group-append" data-target="#reservationdatetime2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <div class="input-group datetime col-sm-3" id="reservationdatetime3" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime3" name="deadline" placeholder="end date">
                            <div class="input-group-append" data-target="#reservationdatetime3" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- End Form -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeItem()">Close</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="saveItem()">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script type="text/javascript">

    //Script untuk Drag and Drop Card
    const todos = document.querySelectorAll(".todo");
    const all_status = document.querySelectorAll(".status");

    let draggableTodo = null;

    todos.forEach((todo) => {
        todo.addEventListener("dragstart", dragStart);
        todo.addEventListener("dragend", dragEnd);
    });
    // fungsi saat card mulai digerakkan
    function dragStart() {
        draggableTodo = this;
        setTimeout(() => {
            this.style.display = "none";
        }, 0);
        console.log("dragStart");
    }

    // fungsi saat card selesai di gerakkan
    function dragEnd() {
        //draggableTodo = null;
        setTimeout(() => {
            this.style.display = "block";
        }, 0);
        console.log("dragend");
    }

    all_status.forEach((status) => {
        status.addEventListener("dragover", dragOver);
        status.addEventListener("dragenter", dragEnter);
        status.addEventListener("dragleave", dragLeave);
        status.addEventListener("drop", dragDrop);
    });

    function dragOver(e) {
        e.preventDefault();
        //console.log("dragOver");
    }
    // fungsi saat card memasuki list 
    function dragEnter() {
        this.style.border = "2px dashed #ccc";
        console.log("dragEnter");

    }
    // fungsi saat card keluar dari list
    function dragLeave() {
        this.style.border = "none";
        console.log("dragLeave");
    }
    // fungsi saat card dilepas
    function dragDrop() {
        this.style.border = "none";
        this.appendChild(draggableTodo);

        // this.id untuk mengambil id List
        // draggableTodo.id untuk mengambil id Card
        console.log("dropped", "ID CARD:", draggableTodo.id, " ID BOARD :", this.id);

        var card_id = draggableTodo.id
        var board_id = this.id.substring(7);
        //console.log(card_id, board_id);
        //mengaktifkan function updateCard
        updateCard(card_id, board_id);
    }
</script>

<script type="text/javascript">

    function addCard(){
        $('#modal_addcard').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
        $('[name="card"]').val('');
        $("#modal_addcard").modal('show');
    }

    function saveCard(){

        var workspace_id = $('[name="workspace_id"]').val();
        var board_id = $('[name="board_id[]"]').eq(0).val();
        var form_data = new FormData($('#form')[0]);;
        form_data.append('workspace_id', workspace_id);
        form_data.append('board_id', board_id);

        $.ajax({
            url : "<?=base_url()?>spadmin/kanbanboard/saveCard",
            type: "POST",
            data: form_data, //this is formData
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status == 'success') 
                {
                    $('#modal_addcard').modal('hide');
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
                    $('#modal_addcard').modal('hide');
                    toastr.error('Gagal', data.message, {timeOut: 1200});
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Gagal', data.message, {timeOut: 2000});
            }
        });
    }

    function updateCard(card_id,board_id){
        var wp_id = $('[name="workspace_id"]').val();
        $.ajax({
            url : "<?=base_url()?>spadmin/kanbanboard/updateCard",
            type: "GET",
            data:{
                card_id : card_id,
                board_id : board_id
            },
            async:false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status == 'success') 
                {
                    toastr.success('Success', data.message, {timeOut: 1200});
                } else {
                    toastr.error('Gagal', "Card gagal dipindahkan !", {timeOut: 1200});
                    // window.setTimeout(function(){location.reload()},800);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Gagal', data.message, {timeOut: 2000});
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
    }

    function showCard(id=''){
        // get Data Card
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/showCard')?>/" + id,
            dataType : "JSON",
            success: function(data){
                if(data.status == true){
                    $('[name="card_id"]').val(`${id}`);
                    $('.namacard').html(`<a onclick="editnamacard('${id}')"><i class="fas fa-paint-brush"></i> ${data.data.namacard} </a>`);
                    if(data.data.deskripsi){
                        $('[name="deskripsi"]').val(`${data.data.deskripsi}`);
                    }
                    if(data.data.duedateCard){
                        $('[name="duedate"]').val(`${data.data.duedateCard}`);
                    }
                    $('.assignmentname').html(`${data.data.assignmentname}`);

                    //card label
                    if(data.data.cardlabel){
                        var html='';
                        $.each(data.data.cardlabel, function (index, val) {
                            html += `<span class="badge" style="background-color:${val.color};color:white;margin-left : 5px"> ${val.label}</span>`;
                        });
                        $('.labelcards').html(html);
                    }

                    //project file
                    if(data.data.attachment){
                        var html='';
                        $.each(data.data.attachment, function (index, val) {
                            html += `<li class="lampiran_${val.attachment_id} row">
                                        <i class="far fa-fw fa-file"></i> 
                                        <a href="<?=base_url()?>assets/uploads/file_project/${val.files}" class="btn-link text-secondary col-sm-10" target="_blank">
                                            ${val.attachment}
                                        </a> 
                                        <a href="javascript:deleteAttachment('${val.attachment_id}')" title="delete" class="delhover text-sm"><i class="fas fa-trash"></i></a>
                                    </li>`;
                        });
                        $('.listfile').html(html);
                    }

                    // komentar
                    if(data.data.komentar){
                        var html = '';
                        $.each(data.data.komentar, function (index, val) {
                            if(val.nama == data.data.me){
                                var positionA='right';
                                var positionB='right';
                                var positionC='left';
                            }else{
                                var positionA='';
                                var positionB='left';
                                var positionC='right';
                            }

                            html += `<div class="direct-chat-msg ${positionA}">
                                        <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-${positionB}">${val.nama}</span>
                                        <span class="direct-chat-timestamp float-${positionC}">${val.tglpost}</span>
                                        </div>
                                        <img class="direct-chat-img" src="<?=base_url()?>assets/uploads/foto/${val.foto}" alt="message user image">
                                        <div class="direct-chat-text">
                                            ${val.comment}
                                        </div>
                                    </div>`;
                        });
                        $('.direct-chat-messages._comment').html(html);
                    }

                    // list pekerjaan (item)
                    if(data.data.carddetail){
                        var html='';
                        var el = 0;
                        $.each(data.data.carddetail, function (index, val) {
                            el++;

                            if(val.duedate){
                                var duedate = `<small class="badge badge-danger" title="${val.startdate} s/d ${val.duedate}"><i class="far fa-clock"></i> ${data.data.duedate[val.carddetail_id]} </small>`;
                            }else{
                                var duedate = '';
                            }

                            if(data.data.assignmentItem[val.carddetail_id]){
                                var person = `<small class="badge badge-info"><i class="far fa-user"></i> ${data.data.assignmentItem[val.carddetail_id]} </small>`;
                            }else{
                                var person = ``;
                            }

                            var checked = val.finish == 'Yes' ? 'checked' : '';

                            if(checked){
                                html += `<li class="list_${val.carddetail_id} done">`;
                            }else{
                                html += `<li class="list_${val.carddetail_id}">`;
                            }

                                html += `<div class="row">
                                            <div class="col-md-2">
                                                <span class="handle">
                                                <i class="fas fa-align-justify"></i>
                                                </span>
                                                <!-- checkbox -->
                                                <div  class="icheck-primary d-inline ml-2">
                                                    <input type="checkbox" value="Yes" name="finish[]" ${checked} id="todoCheck${el}" onclick="checklistItem('${val.carddetail_id}','todoCheck${el}')">
                                                    <label for="todoCheck${el}"></label>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <span class="text" style="font-weight : unset">
                                                    ${val.list}
                                                </span>
                                                <hr style="padding:0;margin:0;border-top: 1px dotted rgba(0,0,0,.1);">
                                                ${duedate} <!-- duedate -->
                                                ${person} <!-- person -->

                                                <div class="tools">
                                                    <a href="javascript:editItem('${val.carddetail_id}')" title="Edit"><i class="fas fa-edit tools-info"></i></a>
                                                    <a href="javascript:hapusItem('${val.carddetail_id}')" title="Delete"><i class="fas fa-trash tools-danger"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>`;
                        });
                        $('.todo-list').html(html);

                        // list option assignment item
                        var optionAssignment='<option value="">Pilih Assignment</option>';
                        if(data.data.assignmentCard){
                            $.each(data.data.assignmentCard, function (index, val) {
                                optionAssignment +=`<option value="${val.pengguna_id}">${val.nama}</option>`; 
                            });
                        }
                        $(".custom-select").empty().append(optionAssignment);
                    }

                    // list history card
                    if(data.data.cardhistory){
                        var html='';
                        html +=`<div class="timeline"><br>`;
                        $.each(data.data.cardhistory, function (index, val) {
                            html+=`<div>
                                        <i class="fas fa-info bg-info"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> ${val.tglpost}</span>
                                            <h3 class="timeline-header no-border"> ${val.history}</h3>
                                        </div>
                                    </div>`;
                        });
                        html+=`<div>`;

                        $('.direct-chat-messages._history').html(html);
                    }

                    $("#modal_card").modal('show');
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Opps..',
                        text: 'Card tidak ditemukan !!',
                    });
                }
            }
        });
    }

    function addItem(){
        $('#modal_item').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
        $('#modal_card').addClass('opacity');
        $('#modal_item').modal('show');
    }

    function closeItem(){
        var cardID = $('[name="card_id"]').val();
        // reset form input normal
        $('#form_item')[0].reset();
        $('[name="carddetail_id"]').val('');
        $('[name="assignment_list"] :selected').removeAttr('selected');
        $('[name="deadline"]').val('');
        $('#modal_item').modal('hide');

        $('#modal_card').removeClass('opacity');
        $('#modal_card').css({'overflow': 'scroll'});
    }

    function saveItem(){
        var cardID = $('[name="card_id"]').val();
        var form_data = new FormData($('#form_item')[0]);
        form_data.append('card_id', cardID);

        var url = $('[name="carddetail_id"]').val() == '' ? "saveItem" : "updateItem";

        $.ajax({
            url : "<?=base_url()?>spadmin/kanbanboard/"+url,
            type: "POST",
            data: form_data, //this is formData
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status == 'success') 
                {
                    closeItem();
                    showCard(cardID);

                } else if (data.status == 'invalid'){

                    html = `<div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-info"></i> Alert !</h5>
                                ${data.message}
                            </div>`;

                    $('#message_item').empty().html(html);
                    $('#message_item').show();
                    
                } else {
                    $('#modal_item').modal('hide');
                    toastr.error('Gagal', data.message, {timeOut: 1200});
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
            }
        });
    }

    function editItem(id){
        var cardID = $('[name="card_id"]').val();

        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/editItem')?>/"+id,
            dataType : "JSON",
            success: function(data){
                if(data.data){
                    $('[name="carddetail_id"]').val(data.respon.carddetail_id);
                    if(data.respon.list){
                        $('[name="list"]').val(data.respon.list);
                    }
                    if(data.respon.duedate){
                        $('[name="deadline"]').val(data.respon.duedate);
                    }
                    if(data.respon.startdate){
                        $('[name="startdate"]').val(data.respon.startdate);
                    }
                    if(data.respon.assignment_list){
                        $('[name="assignment_list"] option[value="'+data.respon.assignment_list+'"]').attr('selected', 'selected');
                    }

                    $('#modal_item').modal({backdrop: 'static', keyboard: false})  //Nonaktifkan klik di luar area modal bootstrap untuk menutup modal
                    $('#modal_item').modal('show');
                    $('#modal_card').addClass('opacity');
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

    function hapusItem(id=''){
        Swal.fire({
            title: 'Apakah anda yakin ?',
            text: "Apakah anda yakin menghapus item ini ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Yakin !'
            }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : "GET",
                    url  : "<?php echo base_url('spadmin/kanbanboard/hapusItem')?>/" + id,
                    dataType : "JSON",
                    success: function(data){
                        if(data.data){
                            $(`.list_${id}`).remove();
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            });
                        }
                    }
                });
            }
        });
    }

    function checklistItem(id, finish=''){
        var cardID = $('[name="card_id"]').val();

        var finishVal = '';
        if($(`#${finish}`).is(":checked")){
            finishVal = 'Yes';
        }else{
            finishVal = 'No';
        }
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/checklistItem')?>",
            data : {
                carddetail_id : id,
                finish        : finishVal
            },
            dataType : "JSON",
            success: function(data){
                if(data.data){
                    showCard(cardID);
                    toastr.success('Success', "Item berhasil di update.", {timeOut: 1200});
                    
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

    function saveDeskripsi(){
        var cardID = $('[name="card_id"]').val();
        var deskripsi = $('[name="deskripsi"]').val();

        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/card/saveDeskripsi')?>",
            data : {
                cardid : cardID,
                deskripsi : deskripsi
            },
            dataType : "JSON",
            success: function(data){
                if(data.status == 'success'){
                    toastr.success('Success', "Card berhasil di update !", {timeOut: 1200});
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
            }
        });
    }

    function saveDuedate(){
        var cardID = $('[name="card_id"]').val();
        var duedate = $('[name="duedate"]').val();

        if(duedate){
            $.ajax({
                type : "POST",
                url  : "<?php echo base_url('spadmin/kanbanboard/card/saveDuedate')?>",
                data : {
                    cardid : cardID,
                    duedate : duedate
                },
                dataType : "JSON",
                success: function(data){
                    if(data.status == 'success'){
                        toastr.success('Success', "Card berhasil di update !", {timeOut: 1200});
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
                }
            });
        }
    }

    function deleteAttachment(id){
        Swal.fire({
            title: 'Apakah anda yakin ?',
            text: "Apakah anda yakin menghapus file ini ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Yakin !'
            }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : "GET",
                    url  : "<?php echo base_url('spadmin/kanbanboard/card/deleteFile')?>/" + id,
                    dataType : "JSON",
                    success: function(data){
                        if(data.data){
                            $(`.lampiran_${id}`).remove();
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            });
                        }
                    }
                });
            }
        });
    }

    function join(){
        var cardID = $('[name="card_id"]').val();
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/card/joinTeam')?>",
            data : {
                card_id : cardID
            },
            dataType : "JSON",
            success: function(data){
                if(data.data == true){
                    toastr.success('Success', "Card berhasil di update !", {timeOut: 1200});
                    showCard(cardID);
                }else if (data.data == 'exist'){
                    toastr.warning('Warning', "Anda sudah terdaftar di team ini !!", {timeOut: 1200});
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            }
        });
    }

    function setLabel(labelid){
        var cardID = $('[name="card_id"]').val();
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/card/setLabel')?>",
            data : {
                card_id : cardID,
                label_id : labelid
            },
            dataType : "JSON",
            success: function(data){
                if(data.data == true){
                    showCard(cardID);
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            }
        });
    }

    function deleteCard(){
        var cardID = $('[name="card_id"]').val(); 
        Swal.fire({
            title: 'Apakah anda yakin ?',
            text: "Apakah anda yakin menghapus card ini ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Yakin !'
            }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : "GET",
                    url  : "<?php echo base_url('spadmin/kanbanboard/card/deleteCard')?>/" + cardID,
                    dataType : "JSON",
                    success: function(data){
                        if(data.data){
                            toastr.success('Success', data.message, {timeOut: 1200});
                            window.setTimeout(function(){location.reload()},800);
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            });
                        }
                    }
                });
            }
        });
    }

    function reload_kanbanboard(wp_id){
        var unitkerjaid = $('#filterUnitkerja').val();
        $.ajax({
            url: "<?php echo base_url() . 'spadmin/kanbanboard/getData' ?>/" + wp_id +'/'+ unitkerjaid,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if(data.data){
                    for(var i=0; i<data.datas.board_id.length;i++){
                        board_id = data.datas.board_id[i];
                        $(`#idboard${board_id}`).empty();

                        if(typeof data.datas[board_id].card_id !== 'undefined'){
                            var dtcard = data.datas[board_id];
                            var card_id = data.datas[board_id].card_id;

                            for(var j=0; j<data.datas[board_id].card_id.length; j++){

                                //create element div class="card card-light card-outline todo"
                                const elementcard = document.createElement("div");
                                elementcard.classList.add("card","card-light","card-outline","todo");
                                elementcard.setAttribute("draggable", "true");
                                elementcard.setAttribute("id", card_id[j]);
                                elementcard.setAttribute("onclick", `showCard('${card_id[j]}')`);

                                //create element div class="card-header" +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                                const cardheader = document.createElement("div");
                                cardheader.classList.add("card-header");

                                //tag h5
                                const h5 = document.createElement("h5");
                                h5.classList.add("card-title","text-md");
                                const text = document.createTextNode(`${dtcard.namacard[j]}`);
                                h5.appendChild(text);
                                cardheader.appendChild(h5);

                                // create element div class="card-tools pr-2 pt-2"
                                const cardtools = document.createElement("div");
                                cardtools.classList.add("card-tools","pr-2","pt-2");

                                if(dtcard.label[j].length > 0){
                                    $.each(dtcard.label[j], function (index, val) {
                                        cardtools.innerHTML +=`<span class="badge" style="background-color:${val.color};color:white;margin-right:4px">${val.label}</span>`;
                                    });
                                }
                                cardheader.appendChild(cardtools);
                                elementcard.appendChild(cardheader);

                                //create element div class="card-body" ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                                const cardbody = document.createElement("div");
                                cardbody.classList.add("card-body");

                                //tag a
                                const tagA = document.createElement("a");
                                tagA.classList.add("text-sm","user-block");

                                if(dtcard.assignment[j].length > 0){
                                    $.each(dtcard.assignment[j], function (index, val) {
                                        tagA.innerHTML +=`<img alt="Avatar" class="direct-chat-img" src="<?=base_url()?>/assets/uploads/foto/${val.foto}" draggable="false" ondragstart="return false;" title="${val.nama}">`;
                                    });
                                }
                                cardbody.appendChild(tagA);

                                const span = document.createElement("span")
                                span.classList.add("float-right");
                                span.innerHTML +=`
                                            <a class="text-gray text-sm">
                                                ${dtcard.deadline[j]}
                                                <i class="far fa-comments mr-1"></i> Comments (${dtcard.jumlahkomentar[j]})
                                            </a>
                                        </span>`;
                                cardbody.appendChild(span);
                                elementcard.appendChild(cardbody);

                                $(`#idboard${board_id}`).append(elementcard);
                                elementcard.addEventListener("dragstart", dragStart);
                                elementcard.addEventListener("dragend", dragEnd);
                            }
                        }
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function editnamacard(id){
        $.ajax({
            type : "GET",
            url  : "<?php echo base_url('spadmin/kanbanboard/card/getCard')?>/"+id,
            dataType : "JSON",
            success: function(data){
                if(data.data){
                    $('.namacard').hide();
                    $('.inputnamacard').show();
                    $('[name="cardname"]').val(`${data.respon.namacard}`);
                }else{
                    toastr.warning('Gagal', 'Data card tidak ditemukan', {timeOut: 2000});
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Gagal', 'Gagal memproses data !', {timeOut: 2000});
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
    }

    function updateNamacard(){
        var cardID = $('[name="card_id"]').val();
        var cardname = $('[name="cardname"]').val();
        if(cardname != ''){
            $.ajax({
                type : "POST",
                url  : "<?php echo base_url('spadmin/kanbanboard/card/updateNamaCard')?>",
                data : {
                    cardid : cardID,
                    namacard : cardname,
                },
                dataType : "JSON",
                success: function(data){
                    if(data.status == 'success'){
                        $('.inputnamacard').hide();
                        showCard(`${cardID}`);
                        $('.namacard').show();
                        toastr.success('Success', "Card berhasil di update !", {timeOut: 1200});
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
                }
            });
        }
    }
</script>

<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function(t) {

        $('#sending').on("click", function(e){
            var cardID = $('[name="card_id"]').val();
            var komentar = $('[name="chatbox"]').val();
            var form_data = new FormData($('#form_item')[0]);
            form_data.append('card_id', cardID);
            form_data.append('komentar', komentar);

            if(komentar){
                $.ajax({
                    url : "<?=base_url()?>spadmin/kanbanboard/sendcomment",
                    type: "POST",
                    data: form_data, //this is formData
                    processData:false,
                    contentType:false,
                    cache:false,
                    async:false,
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == 'success') 
                        {
                            $('[name="chatbox"]').val('');
                            showCard(cardID);
                            toastr.success('Success', "Card berhasil di update !", {timeOut: 1000});
                        } else {
                            toastr.error('Gagal', data.message, {timeOut: 1200});
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
                    }
                });
            }
        });
        
        $('#fileupload').on("change", function(e){

            if(typeof $(this)[0].files[0] !== 'undefined'){
                size = $(this)[0].files[0].size/1024;
                
                if(size > 5300){
                    $(this).val('');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning !',
                        text: 'Ukuran file yang diupload melebihi dari 5 Mb !'
                    });

                }else{

                    // execute
                    var file = $(this)[0].files[0];
                    var cardID = $('[name="card_id"]').val();

                    var formData = new FormData();
                    formData.append("card_id", cardID);
                    formData.append("file", file);

                    $.ajax({
                        url  : "<?php echo base_url('spadmin/kanbanboard/card/saveFile')?>",
                        type : "POST",
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: formData,
                        enctype: 'multipart/form-data',
                        dataType : "JSON",
                        success: function(data){
                            if(data.status == 'success'){
                                $('#fileupload').val('');
                                toastr.success('Success', "Card berhasil di update !", {timeOut: 1200});

                                showCard(cardID);
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            toastr.error('Error', 'Upss.. Terjadi kesalahan !!', {timeOut: 2000});
                        }
                    });
                }
            }
        });

        setInterval(function() {
            if (($('#modal_addcard').hasClass('show') == true) ||
                ($('#modal_card').hasClass('show') == true) ||
                ($('#modal_item').hasClass('show') == true))
            {
                // Jika user masih di dalam modal tidak ada aksi

            } else {
                console.log("reload");
                var wp_id = $('[name="workspace_id"]').val();
                reload_kanbanboard(wp_id);
            }
        }, 5000);
    });

    function filter_kanbanboard(){
        console.log('filter');
        var wp_id = $('[name="workspace_id"]').val();
        reload_kanbanboard(wp_id);
    }
</script>