<?php
use Dompdf\Dompdf;
defined('BASEPATH') OR exit('No direct script access allowed');
class Restaurant extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->library('session');
    $this->load->library('pdf');
    $restaurantData=$this->session->userdata('restaurantData');
    if($restaurantData['email_id']!=''){
     $this->load->model('restaurant/Restaurantmodel');
     $this->load->library('pagination');  
     $this->load->library('S3');
     require APPPATH .'helpers/Common.php';  
     date_default_timezone_set('UTC');
   }else{
    redirect('restaurant/login');
  }
}	
 
//===========dashbaord==========//
public function dashboard(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/dashboard');
}

//========get_dashboard_data================//
public function get_dashboard_data(){
 $postData = $this->input->post();
 $data = $this->Restaurantmodel->get_dashboard_data($postData);
 echo json_encode($data);  
}

//===========profile==========//

public function profile(){
  $this->check_token();
  $restaurantData=$this->session->userdata('restaurantData');
  $result['data']=$this->Restaurantmodel->profile($restaurantData['restaurant_id']);
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/profile',$result);
}

//===========complete_profile==========//

public function complete_profile(){
  $this->check_token();
  $restaurantData=$this->session->userdata('restaurantData');
  $result['menu']=$this->Restaurantmodel->restaurant_menu($restaurantData['restaurant_id']);
  $result['data']=$this->Restaurantmodel->complete_profile($restaurantData['restaurant_id']);
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/complete_profile',$result);
}

//===========complete_profile_process============//

public function complete_profile_process(){
  $post=$this->input->post();
  // $tableData=[];
  // for ($i=0; $i <count($post['AllID']) ; $i++) { 
  //   $tableData[]=["tables"=>$post['dynamictable'][$i],"per_person"=>$post['AllID'][$i],"table_ids"=>implode(',',$post['tableID'.$post['AllID'][$i]]),"restaurant_id"=>$post['id'],"added_on"=>date('Y-m-d H:i:s')];  
  // }
  if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $common=new Common();
    $image_name=$common->generateRandomString();
    $picture = rand(100, 999) . '-' . $image_name.'.'.$ext;
    $temp_name = $_FILES['image']['tmp_name'];
    $folder = 'RestaurantImages';
    $restaurant_image =$this->fileUpload($picture,$temp_name,$folder);
  }else{
    $restaurant_image=$post['old_image'];  
  }
  $data=["restaurant_id"=>$post['id'],"restaurant_image"=>$restaurant_image,"restaurant_name"=>$post['restaurant_name'],"updated_on"=>date('Y-m-d H:i:s'),"location"=>$post['location'],"lat"=>$post['lat'],"lng"=>$post['lng'],"radius"=>$post['range'],"open_time"=>$post['start_time'],"close_time"=>$post['end_time'],"open_days"=>implode(',',$post['days']),"total_table"=>"","table_person"=>"","profile_status"=>"Complete"];
  $data1=["mobile_no"=>$post['phone'],"country_code"=>"+".$post['country_code'],"country_lang_code"=>strtoupper($post['country_lang_code']),"user_id"=>$post['id']];
  $result=$this->Restaurantmodel->complete_profile_process($data,$data1);
  if($result==1){
    echo 1;die;
  }else{
    echo 0;die;
  }  
} 


//============Check Email================//
public function checkvendorExist(){
  $post = $this->input->post();
  if(!empty($post['email']) && isset($post['email'])){
   $value=$post['email'];
 }
 else if(!empty($post['phone']) && isset($post['phone'])){
   $value=$post['phone'];
 }
 $response=$this->Restaurantmodel->checkvendorExist($value);
 if($response==1)
  echo 1;die;
}

//============Check Email================//
public function checkvendorExist1(){
  $post = $this->input->post();
  if(!empty($post['email']) && isset($post['email'])){
   $value=$post['email'];
 }
 else if(!empty($post['phone']) && isset($post['phone'])){
   $value=$post['phone'];
 }
 $response=$this->Restaurantmodel->checkvendorExist1($value,$post['id']);
 if($response==1)
  echo 1;die;
}

//===========Delivery agent list===========//
public function delivery_agent_list(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/delivery_agent_list');
}

//===========get delivery agent list===========//
public function get_delivery_agent_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/delivery-agent-list';
  $config["total_rows"] =$this->Restaurantmodel->get_delivery_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_delivery_agent_list($config["per_page"],$start,$post),
    "data1"=>$this->Restaurantmodel->count_delivery_agent($post)
  );
  echo json_encode($output);
}
//===========delivery_agent_detail===========//
public function delivery_agent_detail(){
  $this->check_token();
  $id=$this->input->get('id');
  $result['data']=$this->Restaurantmodel->delivery_agent_detail($id);
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/delivery_agent_detail',$result);
}
//===========variant_list===========//
public function variant_list(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/variant_list');
}

//===========get_variant===========//
public function get_variant(){
 $postData = $this->input->post();
 $data = $this->Restaurantmodel->get_variant($postData);
 echo json_encode($data);
}

//==============add_variant============//

public function add_variant(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/add_variant');
}
//==========add_variant_process===============//

public function add_variant_process(){
  $this->check_token();
  $post=$this->input->post();
  $restaurantData=$this->session->userdata('restaurantData');
  $data=["variant_name"=>$post['variant_name'],"currency"=>$post['currency'],"price"=>$post['price'],"status"=>"1","added_on"=>date('Y-m-d H:i:s'),"restaurant_id"=>$restaurantData['restaurant_id']];
  $result=$this->Restaurantmodel->add_variant_process($data);
  if($result==1){
    echo 1;die;
  }else{
    echo 0;die;
  }  
}
//===========Varaint Status===============//
public function variant_status(){
  $this->check_token();
  $variant_id=$this->input->post('id');
  $status=$this->input->post('active');
  $data=["variant_id"=>$variant_id,"status"=>$status];
  $response=$this->Restaurantmodel->variant_status($data);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 }
}
 //=================edit Variants=============//
public function edit_variant(){
  $variant_id=$this->input->get('id');
  $result['data']=$this->Restaurantmodel->edit_variant($variant_id);
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/edit_variant',$result);
}   

  //========edit_variant_process==========//
public function edit_variant_process(){
  $post=$this->input->post();
  $data=["variant_name"=>$post['variant_name'],"currency"=>$post['currency'],"price"=>$post['price'],"variant_id"=>$post['variant_id']];
  $result=$this->Restaurantmodel->edit_variant_process($data);
  if($result==1){
    echo 1;die;
  }else{
    echo 0;die;
  }  
}

//============deletevariant ================//
public function deletevariant(){
  $this->check_token();
  $variant_id=$this->input->get('variant_id');  
  $result=$this->Restaurantmodel->deletevariant($variant_id);
  if($result==1){
   redirect('restaurant/variant-list');
 }else{
   redirect('restaurant/variant-list');
 }
}
//================Menu List==========//
public function menu_list(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/menu_list');
}

//===========get_menu_list===========//
public function get_menu_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/menu-list';
  $config["total_rows"] =$this->Restaurantmodel->menu_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_menu_list($config["per_page"],$start,$post)
  );
  echo json_encode($output);
}

 //=================add menu=============//
public function add_menu(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/add_menu');
}   

  //========add_menu_process==========//
public function add_menu_process(){
  $post=$this->input->post();
  $restaurantData=$this->session->userdata('restaurantData');
  if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $common=new Common();
    $image_name=$common->generateRandomString();
    $picture = rand(100, 999) . '-' . $image_name.'.'.$ext;
    $temp_name = $_FILES['image']['tmp_name'];
    $folder = 'Menu';
    $category_image =$this->fileUpload($picture,$temp_name,$folder);
  }else{
    $category_image="";  
  }
  $data=["image"=>$category_image,"category_name"=>$post['category_name'],"status"=>'1',"added_on"=>date('Y-m-d H:i:s'),"description"=>$post['description'],"restaurant_id"=>$restaurantData['restaurant_id']];
  $result=$this->Restaurantmodel->add_menu_process($data);
  if($result==1){
    echo 1;die;
  }else{
    echo 0;die;
  }  
}

//=========Manage Menu Status============//

public function menu_status(){
  $this->check_token();
  $category_id=$this->input->post('id');
  $status=$this->input->post('active');
  $data=["category_id"=>$category_id,"status"=>$status];
  $response=$this->Restaurantmodel->menu_status($data);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 }
}
 //=================edit menu=============//
public function edit_menu(){
  $category_id=$this->input->get('id');
  $result['data']=$this->Restaurantmodel->edit_menu($category_id);
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/edit_menu',$result);
}   

  //========edit_menu_process==========//
public function edit_menu_process(){
  $post=$this->input->post();
  if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $common=new Common();
    $image_name=$common->generateRandomString();
    $picture = rand(100, 999) . '-' . $image_name.'.'.$ext;
    $temp_name = $_FILES['image']['tmp_name'];
    $folder = 'Menu';
    $category_image =$this->fileUpload($picture,$temp_name,$folder);
  }else{
    $category_image=$post['old_image'];  
  }
  $data=["image"=>$category_image,"category_name"=>$post['category_name'],"added_on"=>date('Y-m-d H:i:s'),"description"=>$post['description'],"category_id"=>$post['category_id']];
  $result=$this->Restaurantmodel->edit_menu_process($data);
  if($result==1){
    echo 1;die;
  }else{
    echo 0;die;
  }  
}


//============deletemenu ================//

public function deletemenu(){
  $this->check_token();
  $category_id=$this->input->get('category_id');  
  $result=$this->Restaurantmodel->deletemenu($category_id);
  if($result==1){
   redirect('restaurant/menu-list');
 }else{
   redirect('restaurant/menu-list');
 }
}


//==============setting=================//
public function setting(){
 $this->check_token();
 $this->load->view('restaurant/header');
 $this->load->view('restaurant/setting'); 
}


//============Notification=============//
public function notification(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/notification');  
}

//===========send_notification================//

public function send_notification(){
  $post=$this->input->post();
  $result=$this->Restaurantmodel->send_notification($post);
  echo $result;die();

}


/*=========Function for change password ==========*/
public function change_password(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/change_password');
} 

public function change_password_process(){
  $this->check_token();
  $post=$this->input->post();
  $restaurantData=$this->session->userdata('restaurantData');
  $result=$this->Restaurantmodel->change_password_process($post,$restaurantData['restaurant_id']);
  if($result==1){  
   echo 1;die;    
 }else{
   echo 0;die;
 }
}

//=================FAQ=======================//

public function faq(){
  $this->check_token();
  $result['data']=$this->Restaurantmodel->faq();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/faq',$result); 
}

//=================terms & condition=======================//

function terms_condition(){
  $this->check_token();
  $result['data']=$this->Restaurantmodel->terms_condition();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/terms_condition',$result); 
}

//==================update_terms=================//
function update_terms(){
  $this->check_token();
  $post=$this->input->post();
  $response=$this->Restaurantmodel->update_terms($post);
  if($response==1){
    echo 1;die;
  }else{
    echo 0;die;
  }
}

//=================about_us=======================//

public function about_us(){
  $this->check_token();
  $result['data']=$this->Restaurantmodel->about_us();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/about_us',$result); 
}

//==================update_about_us=================//
public function update_about_us(){
  $this->check_token();
  $post=$this->input->post();
  $response=$this->Restaurantmodel->update_about_us($post);
  if($response==1){
    echo 1;die;
  }else{
    echo 0;die;
  }
}

//=================privacy_policy=======================//

public function privacy_policy(){
  $this->check_token();
  $result['data']=$this->Restaurantmodel->privacy_policy();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/privacy_policy',$result); 
}

//==================update_about_us=================//
public function update_privacy_policy(){
  $this->check_token();
  $post=$this->input->post();
  $response=$this->Restaurantmodel->update_privacy_policy($post);
  if($response==1){
    echo 1;die;
  }else{
    echo 0;die;
  }
}

//============Add Faq=================//

public function add_faq(){
  $this->check_token();
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/add_faq');   
}

//==============add_faq_process============//

public function add_faq_process(){
  $this->check_token();
  $post=$this->input->post();
  $response=$this->Restaurantmodel->add_faq_process($post);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 }  
}

//=================Get faq With ID===============//
public function get_faq(){
  $this->check_token();
  $postData = $this->input->post();
  $data = $this->Restaurantmodel->get_faq($postData['id']);
  echo json_encode($data);  
}

//===============update_faq===============//

public function update_faq(){
  $this->check_token();
  $post=$this->input->post();
  $response=$this->Restaurantmodel->update_faq($post);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 }  
}

//============delete faq================//
public function deletefaq(){
  $this->check_token();
  $faq_id=$this->input->get('faq_id');
  $result=$this->Restaurantmodel->deletefaq($faq_id);
  if($result==1){
   redirect('restaurant/faq');
 }else{
   redirect('restaurant/faq');
 }
}


//============Use for Check Token==================//

public function check_token(){
  $restaurantData=$this->session->userdata('restaurantData');
  $response=$this->Restaurantmodel->check_token($restaurantData['restaurant_id'],$restaurantData['access_token']);
  if($response==0){
    redirect('restaurant/login');
  }
}


//=================item List===============//
public function item_list(){
  $this->check_token();
  $restaurantData=$this->session->userdata('restaurantData');
  $result['category']=$this->Restaurantmodel->restaurantcategory_list($restaurantData['restaurant_id']); 
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/item_list',$result);  
}
//===========get_item_list===========//
public function get_item_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/item-list';
  $config["total_rows"] =$this->Restaurantmodel->item_count($post);
  $config["per_page"] = 9;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_item_list($config["per_page"],$start,$post)
  );
  echo json_encode($output);
}

//=================Add item===============//
public function add_item(){
  $this->check_token();
  $restaurantData=$this->session->userdata('restaurantData');
  $result['category']=$this->Restaurantmodel->restaurantcategory_list($restaurantData['restaurant_id']); 
  $result['variant']=$this->Restaurantmodel->restaurantvariant_list($restaurantData['restaurant_id']);
  $this->load->view('restaurant/header');
  $this->load->view('restaurant/add_item',$result); 
}
//==============add_item_process==============//

public function add_item_process(){
  $this->check_token();
  $post=$this->input->post();
  $itemImages=[];
  if (isset($_FILES['item_images']['name']) && !empty($_FILES['item_images']['name'])){
    $count=count($_FILES['item_images']['name']);
    for($i=0;$i<$count;$i++){
      if (!empty($_FILES['item_images']['name'][$i])){   
        $ext = pathinfo($_FILES['item_images']['name'][$i], PATHINFO_EXTENSION);
        $common=new Common();
        $image_name=$common->generateRandomString();
        $picture = rand(100, 999) . '-' . $image_name.'.'.$ext;
        $temp_name = $_FILES['item_images']['tmp_name'][$i];
        $folder = 'ItemImages';
        $imgurl =$this->fileUpload($picture,$temp_name,$folder);
        $itemImages[] = $imgurl;
      }
    } 
  }
  $is_special=(!empty($post['is_special'])) ? $post['is_special'] :'0';
  $is_recommended=(!empty($post['is_recommended'])) ? $post['is_recommended'] :'0';
  $is_new=(!empty($post['is_new'])) ? $post['is_new'] :'0';
  $restaurantData=$this->session->userdata('restaurantData');
  $data=["item_name"=>$post['item_name'],"item_description"=>$post['description'],"category_id"=>$post['category_id'],"currency"=>$post['currency'],"item_price"=>$post['item_price'],"quantity"=>0,"added_on"=>date('Y-m-d H:i:s'),"status"=>"1","restaurant_id"=>$restaurantData['restaurant_id'],"is_special"=>$is_special,"is_recommended"=>$is_recommended,"is_new"=>$is_new,"is_customize"=>$post['check']];
  $result=$this->Restaurantmodel->add_item_process($data,$itemImages,$post);
  if($result==1){
    echo 1;die(); 
  }else{
    echo 0;die(); 
  }
}

//=================Edit item===============//
public function edit_item(){
  $this->check_token();
  $item_id=$this->input->get('id');
  $restaurantData=$this->session->userdata('restaurantData');
  $result['data']=$this->Restaurantmodel->edit_item($item_id);
  $result['category']=$this->Restaurantmodel->restaurantcategory_list($restaurantData['restaurant_id']);
  if($result['data']['is_customize']=="1") {
    $result['customization']=$this->Restaurantmodel->customization($item_id);
  }else{
   $result['customization']=[]; 
 }
 $this->load->view('restaurant/header');
 $this->load->view('restaurant/edit_item',$result); 
}
//==============edit_item_process==============//

public function edit_item_process(){
  $this->check_token();
  $post=$this->input->post();
  $itemImages=[];
  $count=count($_FILES['item_images']['name']);
  for($i=0;$i<$count;$i++){
   if (!empty($_FILES['item_images']['name'][$i])){   
    $ext = pathinfo($_FILES['item_images']['name'][$i], PATHINFO_EXTENSION);
    $common=new Common();
    $image_name=$common->generateRandomString();
    $picture = rand(100, 999) . '-' . $image_name.'.'.$ext;
    $temp_name = $_FILES['item_images']['tmp_name'][$i];
    $folder = 'ItemImages';
    $imgurl =$this->fileUpload($picture,$temp_name,$folder);
    $itemImages[] = $imgurl;
  }else {
    if (!empty($post['oldimage'][$i])) {
     $itemImages[]=$post['oldimage'][$i];
   }  
 }
}

$data=["item_id"=>$post['item_id'],"item_name"=>$post['item_name'],"item_description"=>$post['description'],"category_id"=>$post['category_id'],"currency"=>$post['currency'],"item_price"=>$post['item_price'],"quantity"=>0,"is_special"=>$post['is_special'],"is_recommended"=>$post['is_recommended'],"is_new"=>$post['is_new'],"is_customize"=>$post['check']];
$result=$this->Restaurantmodel->edit_item_process($data,$itemImages,$post);
if($result==1){
  echo 1;die(); 
}else{
  echo 0;die(); 
}
}
//=========Manage session-status============//

public function item_status(){
  $item_id=$this->input->post('id');
  $status=$this->input->post('active');
  $data=["item_id"=>$item_id,"status"=>$status];
  $response=$this->Restaurantmodel->item_status($data);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 }
}

//=========deleteitem============//
public function deleteitem(){
  $item_id=$this->input->get('item_id');
  $result=$this->Restaurantmodel->deleteitem($item_id);
  if($result==1){
   redirect('restaurant/item-list');
 }else{
   redirect('restaurant/item-list');
 }
}

//============Order list==============//

public function order_list(){
 $restaurantData=$this->session->userdata('restaurantData'); 
 $result['data']=$this->Restaurantmodel->order_list($restaurantData['restaurant_id']); 
 $this->load->view('restaurant/header');
 $this->load->view('restaurant/order_list',$result);  
}

//===========get_request_list===========//
public function get_request_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/order-list';
  $config["total_rows"] =$this->Restaurantmodel->request_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_request_list($config["per_page"],$start,$post)
  );
  echo json_encode($output);
}

//===========get_current_list===========//
public function get_current_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/order-list';
  $config["total_rows"] =$this->Restaurantmodel->current_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_current_list($config["per_page"],$start,$post)
  );
  echo json_encode($output);
}

//===========get history list===========//
public function get_history_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/order-list';
  $config["total_rows"] =$this->Restaurantmodel->history_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_history_list($config["per_page"],$start,$post),
    "data1"=>$this->Restaurantmodel->order_user($post)
  );
  echo json_encode($output);
}

//==============order_confirmed======================//

public function order_confirmed(){
  $id=$this->input->get('id');
  $this->Restaurantmodel->order_notification($id,'confirm');
  $result=$this->Restaurantmodel->order_confirmed($id);
  if($result==1){
   redirect('restaurant/order-list');
 }else{
   redirect('restaurant/order-list');
 } 
}

//==============order_status======================//

public function order_status(){
  $id=$this->input->get('id');
  $order_status=$this->input->get('order_status');
  $this->Restaurantmodel->order_notification($id,$order_status);
  $result=$this->Restaurantmodel->order_status($id,$order_status);
  if($result==1){
   redirect('restaurant/order-list');
 }else{
   redirect('restaurant/order-list');
 } 
}

//========order_accept============//

public function order_accept(){
  $id=$this->input->get('id');
  $this->Restaurantmodel->order_notification($id,'delivered');
  $result['data']=$this->Restaurantmodel->InvoiceData($id);
  $path=base_url().'restaurant_assets/images/logo.png';
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
  $result['data']['image']=$base64; 
  $html=$this->load->view('restaurant/invoice_pdf',$result);
  $html = $this->output->get_output();
  $this->dompdf->loadHtml($html);
  $this->dompdf->setPaper('A4', 'portrait');
  $this->dompdf->render();
  $output = $this->dompdf->output();
  if($output){
    file_put_contents('./restaurant_assets/pdfs/'.$result['data']['order_unique_id'].'_invoice.pdf', $output);
  }
  $temp_name = $_SERVER["DOCUMENT_ROOT"].'/restaurant_assets/pdfs/'.$result['data']['order_unique_id'].'_invoice.pdf';
  $picture = uniqid().".".substr($temp_name, strrpos($temp_name, '.') + 1);
  $folder = 'Invoice/Pdf';
  $pdf_name =$this->fileUpload($picture,$temp_name,$folder);
  $result=$this->Restaurantmodel->order_accept($id,$pdf_name);
  unlink($temp_name);
  if($result==1){
   redirect('restaurant/order-list');
 }else{
   redirect('restaurant/order-list');
 }
}

//==========order_detail==============//
public function order_detail(){
  $id=$this->input->post('id');
  $result=$this->Restaurantmodel->order_detail($id);
  echo json_encode($result); 
}



//====================all_reason=========//
public function all_reason(){
  $post=$this->input->post();
  $result=$this->Restaurantmodel->all_reason();
  echo json_encode($result);die(); 
}

//============order_cancel===============//
public function order_cancel(){
 $post=$this->input->post();
 $this->Restaurantmodel->order_notification($post['id'],'cancel');
 $data=["id"=>$post['id'],"cancel_reason"=>$post['reason_id'],"reason"=>$post['description'],"order_status"=>"Cancel","cancel_by"=>"admin"];
 $result=$this->Restaurantmodel->order_cancel($data);
 if($result==1){
  echo 1;die(); 
}else{
  echo 0;die(); 
} 
}

//==========assign_confirmed==============//
public function assign_confirmed(){
  $address_id=$this->input->post('address_id');
  $result=$this->Restaurantmodel->assign_confirmed($address_id);
  echo json_encode($result); 
}
//=========order_assign==========//
public function order_assign(){
 $post=$this->input->post();
 $this->Restaurantmodel->order_notification($post['id'],'confirm');
 $data=["id"=>$post['id'],"delivery_agent_id"=>$post['delivery_agent_id'],"order_status"=>"Confirmed"];
 $this->Restaurantmodel->driver_notification($post['id'],$post['delivery_agent_id']);
 $result=$this->Restaurantmodel->order_assign($data);
 if($result==1){
  echo 1;die(); 
}else{
  echo 0;die(); 
} 
}

//===============Report List==================//
public function report_list(){
 $this->load->view('restaurant/header');
 $this->load->view('restaurant/report_list');  
}


//=============get_report_list====================//
public function get_report_list(){
  $post=$this->input->post();
  $config = array();
  $config['base_url'] = base_url().'restaurant/report-list';
  $config["total_rows"] =$this->Restaurantmodel->report_count($post);
  $config["per_page"] = 10;
  $config["uri_segment"] = 3;
  $config["use_page_numbers"] = TRUE;
  $config["full_tag_open"] = '<ul class="pagination">';
  $config["full_tag_close"] = '</ul>';
  $config["first_tag_open"] = '<li>';
  $config["first_tag_close"] = '</li>';
  $config["last_tag_open"] = '<li>';
  $config["last_tag_close"] = '</li>';
  $config['next_link'] = '&gt;';
  $config["next_tag_open"] = '<li>';
  $config["next_tag_close"] = '</li>';
  $config["prev_link"] = "&lt;";
  $config["prev_tag_open"] = "<li>";
  $config["prev_tag_close"] = "</li>";
  $config["cur_tag_open"] = "<li class='active' style='pointer-events:none!important; value=''><a href='#'>";
  $config["cur_tag_close"] = "</a></li>";
  $config["num_tag_open"] = "<li>";
  $config["num_tag_close"] = "</li>";
  $this->pagination->initialize($config);
  $page= ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
  $start = ($page - 1) * $config["per_page"];
  $output = array(
    'pagination_link'  => $this->pagination->create_links(),
    'data' => $this->Restaurantmodel->get_report_list($config["per_page"],$start,$post),
    "data1"=>$this->Restaurantmodel->get_report_header($post)
  );
  echo json_encode($output);
}

public function report_export() {
  require_once APPPATH."third_party/Excel/PHPExcel.php";
  require_once APPPATH."third_party/Exceldata.php"; 
  $post=$this->input->get(); 
  $fileName = 'Exceldata/Report '.date("d F Y").'.xlsx'; 
  $result = $this->Restaurantmodel->report_export($post);
  $objPHPExcel = new Exceldata ();
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'S.NO');
  $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
  $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Item Purchase');
  $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Payment'); 
  $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Wallet');
  $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Ambassador');  
  $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Delivery Charge');
  $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Payment Type');  
//============ set Row==================//
  $i=1;
  $rowCount = 2;
  foreach ($result as $val){
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $i );
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $val['order_unique_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  $val['quantity']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $val['currency'].' '.$val['order_price']);
     $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $val['currency'].' '.$val['wallet_amount']);
     $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $val['currency'].' '.$val['ambassador_amount']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,0);
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $val['payment_mode']);
    $rowCount++;
    $i++;
  }
  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
  $objWriter->save($fileName);
 // ===============download file==================//
  header("Content-Type: application/vnd.ms-excel");
  redirect(site_url().$fileName); 
}

//==========order_type_status===============//
public function order_type_status(){
 $this->check_token();
 $post=$this->input->post();
 $response=$this->Restaurantmodel->order_type_status($post);
 if($response==1){
  echo 1;die;
}else{
 echo 0;die;
}  
}
//======change-item-status===============//

public function change_ingredient_status(){
  $post=$this->input->post(); 
  $response=$this->Restaurantmodel->change_ingredient_status($post);
  if($response==1){
    echo 1;die;
  }else{
   echo 0;die;
 } 
}


}///////class====================//
