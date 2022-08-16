<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function addLog($jenis_aksi, $keterangan, $keterangandetail, $pengguna_id='') {
    $CI = get_instance();

    $userId = NULL;
    $tanggal = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];

    $userId = $CI->session->userdata('pengguna_id') ? decrypt($CI->session->userdata('pengguna_id')) : $pengguna_id;
    $dataInsert = array(
        'jenislog' => $jenis_aksi,
        'pengguna_id' => $userId,
        'keterangan' => $keterangan,
        'tgl' => $tanggal,
        'ipaddr' => $ip,
        'status' => 1,
        'keterangandetail' => $keterangandetail,
    );
    
    $CI->Md_logsistem->addLog($dataInsert);
}

function getDuedate($tglduedate, $startdate=''){

    if(strtotime($tglduedate) < strtotime(date('Y-m-d H:i:s'))){
        return 'Has Expired';
    }else{

        if($startdate){
            if(strtotime($startdate) > strtotime(date('Y-m-d H:i:s'))){
                return 'next time';
            }
        }
        // perhitungan waktu tersisa
        $tglNow = $startdate ? new DateTime($startdate) : new DateTime(date('Y-m-d H:i:s'));
        $tglDeadline = new DateTime($tglduedate);
        $diff = date_diff($tglNow, $tglDeadline);
        $day = $diff->days;
        $jam = $diff->h;
        $menit = $diff->i;
        $detik = $diff->s;

        if($day>0){
            $duedate = $day.' hari';
        }else if($jam > 0){
            $duedate = $jam.' jam';
        }else if($menit > 0){
            $duedate = $menit.' menit';
        }else{
            $duedate = $detik.' detik';
        }
        
        return $duedate;
    }
}

function getDuration($firs_date, $secon_date){
    if ($secon_date < $firs_date || $firs_date == "" || $secon_date == "") return null;

    $dteStart         = new DateTime($firs_date); 
    $dteEnd           = new DateTime($secon_date);
    
    $interval         = date_diff($dteStart, $dteEnd);
    
    $DaysToSecconds   = $interval->format('%a')*((60*60)*24);
    $HoursToSeconds   = $interval->format('%H')*(60*60);
    $MinutesToSeconds = $interval->format('%I')*60;
    $SecondsToSeconds = $interval->format('%S');
    
    $TotalSecond = $DaysToSecconds + $HoursToSeconds + $MinutesToSeconds +$SecondsToSeconds;

    $data = array(
        'tahun'      => $interval->format('%y'),
        'hari'       => $interval->d,
        'bulan'      => $interval->m,
        'total_hari' => $interval->format('%a'),
        'jam'        => $TotalSecond/60*60,
        'menit'      => $TotalSecond/60,
        'detik'      => $TotalSecond,
    );

    return (object) $data;
}

function sendMail(){
    $CI = get_instance();
    $CI->load->library('email');
    // $CI->load->helper('indonesia_day');
    date_default_timezone_set('Asia/Jakarta');

    $dtmailbox = $CI->Md_mailbox->getMailbox();

    if ($dtmailbox) {
        $config = array();
        $config['charset'] = 'utf-8';
        $config['useragent'] = 'Codeigniter';
        $config['protocol'] = "smtp";
        $config['mailtype'] = "html";
        $config['smtp_host'] = "smtp_host"; // set smtp_host
        $config['smtp_port'] = 587;
        $config['smtp_timeout'] = "30";
        $config['smtp_user'] = "smtp_user"; // set smtp_user
        $config['smtp_pass'] = "smtp_pass"; // set smtp_pass
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;

        //memanggil library email dan set konfigurasi untuk pengiriman email
        $CI->email->initialize($config);
        //konfigurasi pengiriman
        $CI->email->from($dtmailbox->from);

        $CI->email->bcc('pm@ptpn5.co.id');

        $CI->email->to($dtmailbox->to);
        $CI->email->subject($dtmailbox->subject);
        $CI->email->message($dtmailbox->isi);

        // $CI->Md_mailbox->updateMailbox($dtmailbox->mailbox_id, array('statuskirim' => 'Pending'));
        if ($CI->email->send()) {
            $dtUpdateMailbox = array(
                'tglkirim' => date('Y-m-d H:i:s'),
                'statuskirim' => 'Terkirim',
            );

            $CI->Md_mailbox->updateMailbox($dtmailbox->mailbox_id, $dtUpdateMailbox);
        } else {
            echo $CI->email->print_debugger();

            # Failed send mail

            // $CI->email->from('pm@ptpn5.co.id');
            // $CI->email->to('adesaputrakom@gmail.com');
            // $CI->email->subject('WARNING! Email Sistem Project Management PTPN V tidak berhasil terkirim');
            // $CI->email->message('Dear Admin,<br/><br/>
            //     mailboxid ' . $dtmailbox->mailboxid . ' tidak berhasil terkirim.

            //     <br/><br/>
            //     <br><br><br>Terima Kasih.<br><br>');
            // $CI->email->send();
            // $CI->Md_mailbox->updateMailbox($dtmailbox->mailboxid, array(
            //     'statuskirim' => 'Cancel',
            // ));
        }
    }
}

function kodeswitchakses($val){
    $CI = get_instance();
    $id = $CI->session->userdata('pengguna_id');
    $md5 = md5($val);
    return $md5.'swc'.$id;
}
?>