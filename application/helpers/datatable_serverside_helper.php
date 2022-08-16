<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Get DataTable data
function getDataTable($postData = null, $model)
{
    $CI = get_instance();
    # load Model
    $CI->load->model($model);

    $response = array();

    ## Read value
    $draw = $postData['draw'];
    $start = $postData['start'];
    $rowperpage = $postData['length']; // Rows display per page
    $columnIndex = $postData['order'][0]['column']; // Column index
    $columnName = $postData['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
    $searchValue = $postData['search']['value']; // Search value
    $searchParam = $postData['searching']; // Search parameter key
    $filterParam = $postData['filtering']; // filter parameter key

    ## Custom Search 
    $search_arr = array();
    $searchQuery = "";
    if ($searchValue != '') {
        // $searchValue = str_replace("'","\'",$searchValue); //replace tanda kutip
        $searchValue = addslashes($searchValue); //replace character quote with slashes

        if(is_array($searchParam)){
            if(count($searchParam)>0){

                for($i=0; $i < count($searchParam); $i++){
                    if(count($searchParam) == 1){
                        $searchdata = "( $searchParam[$i] like '%" . $searchValue . "%' )";
                    }else{
                        $searchdata[]= "$searchParam[$i] like /";
                    }
                }

                if(is_array($searchdata)){
                    $searchdata = "( ". str_replace('/', "'%" . $searchValue . "%'", implode(" or ", $searchdata)) . " )";
                }

                $search_arr[] = $searchdata;
            }
        }
    }

    ## Custom search filter
    $filtering = [];
    if(is_array($filterParam)){
        if(count($filterParam)>0){
            for($i=0; $i < count($filterParam); $i++){
                $filtering[$i] = $filterParam[$i];

                $filterValue = $postData[$filtering[$i]];
                if($filterValue){
                    $search_arr[] = " $filtering[$i] ='" . $filterValue . "' ";
                }
            }
        }
    }

    if (count($search_arr) > 0) {
        $searchQuery = implode(" and ", $search_arr);
    }

    ## Total number of records without filtering
    $CI->db->select('count(*) as allcount');
    $records = $CI->$model->getDataTable();
    $totalRecords = $records[0]->allcount;

    ## Total number of record with filtering
    $CI->db->select('count(*) as allcount');
    if ($searchQuery != '')
        $CI->db->where($searchQuery);
    $records = $CI->$model->getDataTable();
    $totalRecordwithFilter = $records[0]->allcount;

    ## Fetch records
    $CI->db->select('*');
    if ($searchQuery != '')
        $CI->db->where($searchQuery);
    $CI->db->order_by($columnName, $columnSortOrder);
    $CI->db->limit($rowperpage, $start);
    $records = $CI->$model->getDataTable();

    $data = array();
    $response['data'] = $records;
    
    ## Response
    $response['meta'] = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
    );

    return $response;
}
