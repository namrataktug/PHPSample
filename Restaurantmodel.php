<?php
class Restaurantmodel extends CI_Model
{

//=========get_dashboard_data============//

  public function get_dashboard_data($post){
   $where="((order_status = 'Cancel') OR (payment_status = '' AND payment_mode = 'online') OR payment_status IN('canceled','failed'))"; 
   $where1="(order_status NOT IN('Delivered','Cancel') AND payment_status NOT IN('canceled','failed') AND ((payment_mode='online' AND payment_status!='') OR (payment_mode='cod' || payment_mode='wallet' )))"; 
   $todaydate=date('Y-m-d');
   $weekstart = date( 'Y-m-d', strtotime('monday this week'));
   $weekend = date("Y-m-d",strtotime('this week +6 days'));
   $month = date('Y-m-01');
   $year=date('Y');
   if($post['date_wise']=="1"){
    $order_received=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $order_delivered=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Delivered')->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $new_customer=$this->db->from("m_user")->where('user_type','user')->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $net_earning=$this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price')->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->where('order_status','Delivered')->get('m_order'); 
    $order_cancelled=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where)->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $order_pending=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where1)->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $order_confirmed=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Confirmed')->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results();
    $on_the_way=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','On the way')->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate)->count_all_results(); 
  }
  else if($post['date_wise']=="2"){
   $order_received=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results();
   $order_delivered=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Delivered')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results();
   $new_customer=$this->db->from("m_user")->where('user_type','user')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results();
   $net_earning=$this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price')->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->where('order_status','Delivered')->get('m_order');
   $order_cancelled=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where)->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results(); 
   $order_pending=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where1)->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results(); 
   $order_confirmed=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Confirmed')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results(); 
   $on_the_way=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','On the way')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart)->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend)->count_all_results(); 
 }
 else if($post['date_wise']=="3"){
   $order_received=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
   $order_delivered=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Delivered')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
   $new_customer=$this->db->from("m_user")->where('user_type','user')->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$month)->count_all_results();
   $net_earning=$this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price')->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->where('order_status','Delivered')->get('m_order');
   $order_cancelled=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where)->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
   $order_pending=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where1)->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
   $order_confirmed=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Confirmed')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results(); 
   $on_the_way=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','On the way')->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results(); 
 }
 else{
  $order_received=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $order_delivered=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Delivered')->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $new_customer=$this->db->from("m_user")->where('user_type','user')->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $net_earning=$this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price')->where('restaurant_id',$post['restaurant_id'])->where('DATE_FORMAT(added_on, "%Y")=',$year)->where('order_status','Delivered')->get('m_order'); 
  $order_cancelled=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $order_pending=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where($where1)->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $order_confirmed=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','Confirmed')->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
  $on_the_way=$this->db->from("m_order")->where('restaurant_id',$post['restaurant_id'])->where('order_status','On the way')->where('DATE_FORMAT(added_on, "%Y")=',$year)->count_all_results();
}
$net=$net_earning->row_array(); 
$result=["order_received"=>$order_received,"order_delivered"=>$order_delivered,"new_customer"=>$new_customer,"net_earning"=>$net['order_price'],"order_cancelled"=>$order_cancelled,"order_pending"=>$order_pending,"order_confirmed"=>$order_confirmed,"on_the_way"=>$on_the_way];
return $result;
}


//===========restaurant_menu=================//

public function restaurant_menu($restaurant_id){
 $this->db->select('category_id,category_name');
 $this->db->from('m_category');
 $this->db->where('restaurant_id',$restaurant_id);
 $this->db->where_not_in('status','2');
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }  
} 

public function profile($restaurant_id){
  $this->db->select("r.restaurant_id,r.restaurant_name,r.restaurant_image,u.country_code,u.mobile_no,r.location,r.profile_status,r.added_on,u.email_id,u.name,IFNULL((Select GROUP_CONCAT(distinct category_name SEPARATOR ',') as menu from m_category where restaurant_id=".$restaurant_id." and status='1'),'') as menu");
  $this->db->from('m_user as u');
  $this->db->join('m_vendors as r','u.user_id=r.restaurant_id','left');
  $this->db->where('r.restaurant_id',$restaurant_id);
  $select=$this->db->get();
  if($select->num_rows()>0){
   $result=$select->row_array(); 
   return $result;
 }else{
   return [];
 }  
} 



 //===========complete_profile=================//
public function complete_profile($restaurant_id){
 $this->db->select('r.*,u.country_code,u.mobile_no,u.email_id,u.country_lang_code'); 
 $this->db->from('m_user as u');
 $this->db->join('m_vendors as r','u.user_id=r.restaurant_id','left');
 $this->db->where('r.restaurant_id',$restaurant_id);
 $select=$this->db->get();
 $result=$select->row_array();
 $this->db->select('tables,per_person,table_ids');
 $this->db->where('restaurant_id',$restaurant_id);
 $select1=$this->db->get('m_restaurant_table');
 if($select1->num_rows()>0){
  $table_data=$select1->result_array();
}else{
  $table_data=[];
}
$result['table_data']=$table_data;
return $result;

} 

//==========complete_profile_process==============//
public function complete_profile_process($data,$data1){
  // $this->db->where('restaurant_id',$data['restaurant_id'])->delete('m_restaurant_table');
  // $this->db->insert_batch('m_restaurant_table',$tableData);
  $this->db->where('user_id',$data1['user_id'])->update('m_user',$data1);
  $update=$this->db->where('restaurant_id',$data['restaurant_id'])->update('m_vendors',$data);
  if($update==1){
    return 1;
  }else{
    return 0;
  } 

}


  //==============checkEmailExist================//

public function checkEmailExist($data){
  $select=$this->db->select('admin_id')
  ->where('email',$data['email'])
  ->where_not_in('admin_id',$data['admin_id'])
  ->get('pv_admin');
  if($select->num_rows()>0){
    return 1;
  }else{
    return 0;
  }                   
}

 //================change_password_process=========//
public function change_password_process($data,$user_id){
 $select=$this->db->where('user_id',$user_id)
 ->where('password',md5($data['old_password']))
 ->get('m_user');
 if($select->num_rows()>0){
  $update=$this->db->where('user_id',$user_id)
  ->set('password',md5($data['new_password']))
  ->update('m_user');
  if($update==1){
   return 1;
 }else{
   return 0;
 }
}         
}
 //=======get_delivery_count================//
public function get_delivery_count($post){
 $todaydate=date('Y-m-d');
 $weekstart = date( 'Y-m-d', strtotime('monday this week'));
 $weekend = date("Y-m-d",strtotime('this week +6 days'));
 $month = date('Y-m-01');
 $year=date('Y');
 $this->db->select('u.user_id,u.name,u.email_id,u.country_code,u.mobile_no,u.assign_city,u.status');

 $this->db->from('m_user as u'); 
 if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")=',$todaydate);
}
else if($post['date_wise']=="2"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$weekstart);
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")<=',$weekend);
}
else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(u.added_on, "%Y")=',$year);
}
$this->db->where('u.user_type','driver');
$this->db->where_not_in('u.status','2');
if($post['search'] != ''){
 $searchQuery="(u.name like '%".$post['search']."%' or u.email_id like '%".$post['search']."%' or u.mobile_no like'%".$post['search']."%' or u.country_code like'%".$post['search']."%' or u.assign_city like'%".$post['search']."%' or IFNULL((select CAST(AVG(rating) as decimal(10,1))as avg_rating from m_delivery_review as r where r.delivery_agent_id=u.user_id),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Delivered'),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Cancel'),0) like'%".$post['search']."%' )";
 $this->db->where($searchQuery);
}
$select=$this->db->get();
$allcount =$select->num_rows();               
return $allcount; 
}

//==========get_delivery_agent_list============//
public function get_delivery_agent_list($limit,$start,$post){
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));  
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('u.user_id,u.name,u.email_id,u.country_code,u.mobile_no,u.assign_city,u.status,IFNULL((select CAST(AVG(rating) as decimal(10,1))as avg_rating from m_delivery_review as r where r.delivery_agent_id=u.user_id),0) as avg_rating,IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status="Delivered"),0) as order_delivered,IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status="Cancel"),0) as order_cancel');
  $this->db->from('m_user as u'); 
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
   $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$weekstart);
   $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")<=',$weekend);
 }
 else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(u.added_on, "%Y")=',$year);
}
$this->db->where('u.user_type','driver');
$this->db->where_not_in('u.status','2');
if($post['search'] != ''){
  $searchQuery="(u.name like '%".$post['search']."%' or u.email_id like '%".$post['search']."%' or u.mobile_no like'%".$post['search']."%' or u.country_code like'%".$post['search']."%' or u.assign_city like'%".$post['search']."%' or IFNULL((select CAST(AVG(rating) as decimal(10,1))as avg_rating from m_delivery_review as r where r.delivery_agent_id=u.user_id),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Delivered'),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Cancel'),0) like'%".$post['search']."%' )";
  $this->db->where($searchQuery);
}
$this->db->limit($limit,$start);
$select=$this->db->get();
if($select->num_rows()>0){
 $result=$select->result_array(); 
}else{
 $result=[]; 
}
return $result;
}
//=================count_delivery_agent===========//

public function count_delivery_agent($post){
 $todaydate=date('Y-m-d');
 $weekstart = date( 'Y-m-d', strtotime('monday this week'));
 $weekend = date("Y-m-d",strtotime('this week +6 days'));
 $month = date('Y-m-01');
 $year=date('Y');
 $this->db->select("IFNULL((COUNT(o.id)),0) as total_delivery");
 $this->db->from('m_user as u');
 $this->db->join('m_order as o','u.user_id=o.delivery_agent_id','LEFT');
 $this->db->where('u.user_type','driver');
 $this->db->where('o.order_status','Delivered');
 $this->db->where_not_in('u.status','2');
 if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")=',$todaydate);
}
else if($post['date_wise']=="2"){
 $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$weekstart);
 $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")<=',$weekend);
}
else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(u.added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(u.added_on, "%Y")=',$year);
}
if($post['search'] != ''){
 $searchQuery="(u.name like '%".$post['search']."%' or u.email_id like '%".$post['search']."%' or u.mobile_no like'%".$post['search']."%' or u.country_code like'%".$post['search']."%' or u.assign_city like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Delivered'),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=u.user_id and o.order_status='Cancel'),0) like'%".$post['search']."%')";
 $this->db->where($searchQuery);
}
$result=$this->db->get()->row();

$this->db->from("m_user");
if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}
else if($post['date_wise']=="2"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart);
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend);
}
else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(added_on, "%Y")>=',$year);
}
if($post['search'] != ''){
 $searchQuery="(name like '%".$post['search']."%' or email_id like '%".$post['search']."%' or mobile_no like'%".$post['search']."%' or country_code like'%".$post['search']."%' or assign_city like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=m_user.user_id and o.order_status='Delivered'),0) like'%".$post['search']."%' or IFNULL((select COUNT(id) from m_order as o where o.delivery_agent_id=m_user.user_id and o.order_status='Cancel'),0) like'%".$post['search']."%')";
 $this->db->where($searchQuery);
}
$this->db->where('user_type','driver');
$this->db->where_not_in('status','2');
$all_delivery_agent=$this->db->count_all_results();
$result=["total_delivery"=>$result->total_delivery,"all_delivery_agent"=>$all_delivery_agent];
return $result;
}

//==========delivery_agent_detail==============//
public function delivery_agent_detail($id){
  $select=$this->db->select('user_id,user_image,name,email_id,country_code,mobile_no,assign_city,assign_area,added_on,IFNULL((select CAST(AVG(rating) as decimal(10,1))as avg_rating from m_delivery_review where delivery_agent_id='.$id.'),0) as avg_rating')->where('user_id',$id)->get('m_user');
  if($select->num_rows()>0){
    $result=$select->row_array();
    $this->db->select('r.rating,r.feedback,u.name,u.user_image');
    $this->db->from('m_delivery_review as r');
    $this->db->join('m_user as u','r.user_id=u.user_id','left');
    $this->db->where('r.delivery_agent_id',$id);
    $select1=$this->db->get();
    if($select1->num_rows()>0){
     $rating=$select1->result_array();
     $result['rating']=$rating; 
   }else{
     $result['rating']=[];
   } 
   return $result;
 }else{
  return [];
}
}
//===============Check Email phone============//
public function checkvendorExist($value){
  $select=$this->db->where('email',$value)
  ->or_where('phone',$value)
  ->get('m_sub_vendors');
  if($select->num_rows()>0){
    return 1;
  }else{
    return 0;
  }                  
}
//===============Check Email phone============//
public function checkvendorExist1($value,$vendor_id){
 $where = "(email=$value or phone = $value)";   
 $select=$this->db->where($where)
 ->where_not_in('vendor_id',$vendor_id)
 ->get('m_sub_vendors');
 if($select->num_rows()>0){
  return 1;
}else{
  return 0;
}                  
}

 //=======menu_count================//
public function menu_count($post){
 $this->db->select('category_id');
 $this->db->from('m_category');
 $this->db->where('restaurant_id',$post['restaurant_id']);
 $this->db->where_not_in('status','2');
 if($post['search'] != ''){
   $searchQuery="(category_name like '%".$post['search']."%' or description like '%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $select=$this->db->get();
 $allcount =$select->num_rows();               
 return $allcount; 
}

//==========get_menu_list============//
public function get_menu_list($limit,$start,$post){
 $this->db->select('category_id,category_name,description,image,status,restaurant_id');
 $this->db->from('m_category');
 $this->db->where('restaurant_id',$post['restaurant_id']);
 $this->db->where_not_in('status','2');
 if($post['search'] != ''){
   $searchQuery="(category_name like '%".$post['search']."%' or description like '%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $this->db->order_by('category_id','desc');
 $this->db->limit($limit,$start);
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }
}


//============add_menu_process======//

public function add_menu_process($data){
 $insert=$this->db->insert('m_category',$data);
 $insert_id=$this->db->insert_id();
 if($insert_id>0){
  return 1;
}else{
  return 0;
}  
}

//=========menu_status============//

public function menu_status($data){
  $select=$this->db->where('category_id',$data['category_id'])
  ->get('m_category');
  if($select->num_rows()>0){
    $this->db->where('category_id',$data['category_id']);
    $this->db->set('status',$data['status']);          
    $update=$this->db->update('m_category');
    $this->db->where('category_id',$data['category_id']);
    $this->db->set('status',$data['status']);          
    $update1=$this->db->update('m_items');
    if($update==1){
      return 1;
    }else{
      return 0;
    }        
  }
  else{
    return 0;
  }
}

//============deletemenu================//

public function deletemenu($category_id){
  $update=$this->db->where('category_id',$category_id)->set('status','2')->update('m_category');
  $update1=$this->db->where('category_id',$category_id)->set('status','2')->update('m_items');
  
  if($update==1){
    return 1;
  }else{
    return 0;
  } 
}

//============edit_menu==============//
public function edit_menu($category_id){
 $this->db->select('category_id,category_name,description,image,status,restaurant_id');
 $this->db->from('m_category');
 $this->db->where('category_id',$category_id);
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->row_array(); 
   return $result;
 }else{
   return [];
 }
}

//============edit_menu_process==========//
public function edit_menu_process($data){
  $update=$this->db->where('category_id',$data['category_id'])->update('m_category',$data);
  if($update==1){
    return 1;
  }else{
    return 0;
  } 
}

//==========get_variant============//
public function get_variant($postData){
 $response = array();
 $draw = $postData['draw'];
 $start = $postData['start'];
  $rowperpage = $postData['length']; // Rows display per page
   $columnIndex = $postData['order'][0]['column']; // Column index
   $columnName = $postData['columns'][$columnIndex]['data'];
   // $columnSortOrder = $postData['order'][0]['dir']; // asc or desc 
    $columnSortOrder = 'DESC'; // asc or desc 
    $searchValue = $postData['search']['value']; // Search value
    $search_arr = array();
    $searchQuery = "";
    if($searchValue != ''){
      $search_arr[] = "(variant_name like '%".$searchValue."%' or price like '%".$searchValue."%') ";
    }

    if(count($search_arr) > 0){
      $searchQuery = implode(" and ",$search_arr);
    }
    $this->db->select('variant_id,variant_name,currency,price,status,added_on');
    $this->db->where('restaurant_id',$postData['restaurant_id']);
    $this->db->where_not_in('status','2');
    $select=$this->db->get('m_variants');
    $records=$select->result();
    $totalRecords =$select->num_rows();

     ## Total number of record with filtering
    $this->db->select('variant_id,variant_name,currency,price,status,added_on');
    $this->db->where('restaurant_id',$postData['restaurant_id']);
    $this->db->where_not_in('status','2'); 
    if($searchQuery != '')
     $this->db->where($searchQuery);
   $select1=$this->db->get('m_variants');
   $records=$select1->result();
   $totalRecordwithFilter =$select1->num_rows();

     ## Fetch records
   $this->db->select('variant_id,variant_name,currency,price,status,added_on');
   $this->db->where('restaurant_id',$postData['restaurant_id']);
   $this->db->where_not_in('status','2');
   if($searchQuery != '')
     $this->db->where($searchQuery);
   $this->db->order_by($columnName, $columnSortOrder);
   $this->db->limit($rowperpage, $start);
   $select2=$this->db->get('m_variants');
   $records=$select2->result();

   $data = array();
   $no = $_POST['start'];
   foreach($records as $record ){
    $no++;
    $action="";
    $action.='<a href="javascript:void(0)" onclick="deletevariant('.$record->variant_id.')"><i class="fa fa-trash del-icon mr-3" aria-hidden="true"></i></a><a href="javascript:void(0)" onclick="edit_variant('.$record->variant_id.')"><i class="fa fa-pencil edit-icon" aria-hidden="true"></i></a>';
    if ($record->status == '1') {
      $action.='<a class="btn btn-danger"  onclick="status(this,'.$record->variant_id.');">Block</a>';
    } else {
      $action.='<a class="btn btn-success"  onclick="status(this,'.$record->variant_id.');">Unblock</a>';
    }
    $data[] = array( 
      "variant_name"=>$record->variant_name,
      "currency"=>$record->currency,
      "price"=>$record->price,
      "added_on"=>date("d M Y", strtotime($record->added_on)),
      "action"=>$action
    ); 
  }

     ## Response
  $response = array(
   "draw" => intval($draw),
   "iTotalRecords" => $totalRecords,
   "iTotalDisplayRecords" => $totalRecordwithFilter,
   "aaData" => $data
 );
  return $response; 
}

//============add_variant_process======//
public function add_variant_process($data){
 $insert=$this->db->insert('m_variants',$data);
 $insert_id=$this->db->insert_id();
 if($insert_id>0){
  return 1;
}else{
  return 0;
}  
}

//=========variant_status============//
public function variant_status($data){
  $select=$this->db->where('variant_id',$data['variant_id'])
  ->get('m_variants');
  if($select->num_rows()>0){
    $this->db->where('variant_id',$data['variant_id']);
    $this->db->set('status',$data['status']);          
    $update=$this->db->update('m_variants');
    if($update==1){
      return 1;
    }else{
      return 0;
    }        
  }
  else{
    return 0;
  }
}

//============deletevariant================//
public function deletevariant($variant_id){
  $update=$this->db->where('variant_id',$variant_id)->set('status','2')->update('m_variants');
  if($update==1){
    return 1;
  }else{
    return 0;
  } 
}


//============edit_variant==============//
public function edit_variant($variant_id){
 $this->db->select('variant_id,variant_name,currency,price,status,added_on');
 $this->db->from('m_variants');
 $this->db->where('variant_id',$variant_id);
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->row_array(); 
   return $result;
 }else{
   return [];
 }
}

//============edit_variant_process==========//
public function edit_variant_process($data){
  $update=$this->db->where('variant_id',$data['variant_id'])->update('m_variants',$data);
  if($update==1){
    return 1;
  }else{
    return 0;
  } 
}
//==============send_notification==================//
public function send_notification($post){
  $restaurantData=$this->session->userdata('restaurantData');  
  $notitype="normal";
  if($post['reciver']=="user"){
    $select=$this->db->select('user_id,user_device_token,user_device_type,allow_notification')->where('user_type','customer')->where('status','1')->get('m_user');
  }else{
    $select=$this->db->select('user_id,user_device_token,user_device_type,allow_notification')->where('user_type','driver')->where('status','1')->get('m_user'); 
  }
  if($select->num_rows()>0){
    $result=$select->result_array();
    foreach ($result as $key => $value) {
      if($value['user_device_token'] != '' && $value['allow_notification']=="1") {
        $NotiData=["title"=>$post['title'],"body"=>$post['body'],"order_type"=>$notitype];
        $common=new Common();
        $res=$common->notification($value['user_device_token'],$value['user_device_type'],$NotiData,$notitype);
      }
      $NotificationData=['sender_id'=>$restaurantData['restaurant_id'],'receiver_id' =>$value['user_id'],'title' =>$post['title'],'message'=>$post['body'],'type'=>$notitype,"sender_type"=>'restaurant',"addded_on"=>date('Y-m-d H:i:s')];
      $this->db->insert('m_notifications',$NotificationData);
    }
    return 1; 
  }else{
    return 2;
  }
}
//==============terms& condition=============//

public function terms_condition(){
  $select=$this->db->select('terms_id,body')
  ->order_by('terms_id','desc')
  ->limit('1')  
  ->get('pv_terms_condition');
  if($select->num_rows()>0){
    $result=$select->row_array();
    return $result;
  }else{
    return [];
  }                 
}

//============update_terms==================//

public function update_terms($post){
  $update=$this->db->where('terms_id',$post['terms_id'])->set('body',$post['body'])->update('pv_terms_condition');
  if($update==1){
    return 1;
  }else{
    return 0;
  }  
}


//==============Faq=============//

public function faq(){
  $select=$this->db->select('id,question,answer')
  ->order_by('id','desc') 
  ->get('pv_faq');
  if($select->num_rows()>0){
    $result=$select->result_array();
    return $result;
  }else{
    return [];
  }                 
}
//==============about_us=============//

public function about_us(){
  $select=$this->db->select('id,body')
  ->order_by('id','desc')
  ->limit('1')  
  ->get('pv_about_us');
  if($select->num_rows()>0){
    $result=$select->row_array();
    return $result;
  }else{
    return [];
  }                 
}


//============ update_about_us==================//

public function update_about_us($post){
  $update=$this->db->where('id',$post['id'])->set('body',$post['body'])->update('pv_about_us');
  if($update==1){
    return 1;
  }else{
    return 0;
  }  
}

//==========privacy_policy==============//
public function privacy_policy(){
  $select=$this->db->select('privacy_id,body')
  ->order_by('privacy_id','desc')
  ->limit('1')  
  ->get('pv_privacy_policy');
  if($select->num_rows()>0){
    $result=$select->row_array();
    return $result;
  }else{
    return [];
  }                 
}


//============ update_privacy_policy==================//

public function update_privacy_policy($post){
  $update=$this->db->where('privacy_id',$post['privacy_id'])->set('body',$post['body'])->update('pv_privacy_policy');
  if($update==1){
    return 1;
  }else{
    return 0;
  }  
}


//=============restaurantcategory_list=================//
public function restaurantcategory_list($id){
 $select=$this->db->select('category_id,category_name')
 ->where('status','1')
 ->where('restaurant_id',$id)
 ->get('m_category');
 if($select->num_rows()>0){
  $result=$select->result_array();
  return $result;
}else{
  return [];
}                   
}

//=============restaurantvariant_list=================//
public function restaurantvariant_list($restaurant_id){
 $select=$this->db->select('variant_id,variant_name')
 ->where('status','1')
 ->where('restaurant_id',$restaurant_id)
 ->get('m_variants');
 if($select->num_rows()>0){
  $result=$select->result_array();
  return $result;
}else{
  return [];
}                   
}

 //=======item_count================//
public function item_count($post){
 $this->db->select('item_id');
 $this->db->from('m_items');
 $this->db->where('restaurant_id',$post['restaurant_id']);
 $this->db->where('category_id',$post['category_id']);
 $this->db->where_not_in('status','2');
 if($post['search'] != ''){
   $searchQuery="(item_name like '%".$post['search']."%' or item_description like '%".$post['search']."%' or item_price like '%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $select=$this->db->get();
 $allcount =$select->num_rows();               
 return $allcount; 
}

//==========get_item_list============//
public function get_item_list($limit,$start,$post){
  $this->db->select('m.item_id,m.item_name,m.item_description,m.currency,m.item_price,m.quantity,im.item_images,m.status');
  $this->db->from('m_items as m');
  $this->db->join('m_item_images as im','m.item_id=im.item_id','left');
  $this->db->where('im.is_primary','1');
  $this->db->where('restaurant_id',$post['restaurant_id']);
  $this->db->where('category_id',$post['category_id']);
  $this->db->where_not_in('status','2');
  $this->db->order_by('m.item_id','DESC');
  if($post['search'] != ''){
   $searchQuery="(item_name like '%".$post['search']."%' or item_description like '%".$post['search']."%' or item_price like '%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $this->db->limit($limit,$start);
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->result_array(); 
   for ($i=0; $i <count($result) ; $i++) { 
    $result[$i]['item_description']=mb_strimwidth($result[$i]['item_description'], 0, 200,'...');
  }
  return $result;
}else{
 return [];
}
}

//==============add_item_process=======================//
public function add_item_process($data,$itemImages,$post){
  $this->db->insert('m_items',$data);
  $last_id=$this->db->insert_id();
  for ($i=0; $i <count($itemImages) ; $i++) { 
    if($i==0){
      $is_cover="1";
    }else{
      $is_cover="0";
    }
    $imagedata=[];  
    $imagedata=["item_images"=>$itemImages[$i],"item_id"=>$last_id,"is_primary"=>$is_cover,"last_updated"=>date('Y-m-d H:i:s')];
    $this->db->insert('m_item_images',$imagedata);
  }
    //============variant insert==============//
  if($post['check']=='1'){
   for ($i=0; $i <count($post['all_id']) ; $i++) {
     $id="";
     $id=$post['all_id'][$i];
     $AddonData=["item_id"=>$last_id,"addon_name"=>$post['addon_name'][$i],"selection_type"=>$post["selection_type$id"],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s'),"min"=>0,"max"=>$post["max$id"],"is_required"=>$post["required$id"]];
     $this->db->insert('m_item_addons',$AddonData);
     ;
     $insert_id="";
     $insert_id=$this->db->insert_id();
     for ($j=0; $j <count($post["name$id"]);$j++) { 
       $ingredientData=["addon_id"=>$insert_id,"name"=>$post["name$id"][$j],"price"=>$post["price$id"][$j],"currency"=>$post['currency'],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
       $this->db->insert('m_addon_ingredients',$ingredientData);
     }
   }
 }  
 //==========end=============//
 return 1; 
}


//==========Edit item================//
public function edit_item($item_id){
  $this->db->select('*');
  $select=$this->db->where('item_id',$item_id)->get('m_items');
  if($select->num_rows()>0){
    $result=$select->row_array();
//==========2nd============//
    $this->db->select('item_image_id,item_images');
    $select1=$this->db->where('item_id',$item_id)->get('m_item_images');
    if($select1->num_rows()>0){
      $result['images']=$select1->result_array();
    }else{
      $result['images']=[];
    }
//========3rd=============//
    $this->db->select('GROUP_CONCAT(iv.variant_id) as variant_ids');
    $this->db->from('m_item_variants as iv');
    $this->db->join('m_variants as v','iv.variant_id=v.variant_id','left');
    $this->db->where('iv.item_id',$item_id);
    $select2=$this->db->get();
    $data=$select2->row_array();
    if(!empty($data['variant_ids'])){
      $result['variant_ids']=explode(',',$data['variant_ids']);
    }else{
      $result['variant_ids']=[];
    }
    return $result;
  } else{
    $result=[];
    $result['images']=[];
    $result['variant_ids']=[];
    return $result; 
  } 
}

//==========edit_item_process===================//
public function edit_item_process($data,$itemImages,$post){
 for ($i=0; $i <count($itemImages) ; $i++) { 
  $query=$this->db->select('item_image_id')->where('item_id',$data['item_id'])->get('m_item_images');
  $oldImagesID=$query->result_array();
  if(isset($oldImagesID[$i]['item_image_id']) && !empty($oldImagesID[$i]['item_image_id'])){
    $query1=$this->db->select('item_image_id')->where('item_image_id',$oldImagesID[$i]['item_image_id'])->where('item_images',$itemImages[$i])->where('item_id',$data['item_id'])->get('m_item_images');
    if($query1->num_rows()==0){
      $this->db->where('item_image_id',$oldImagesID[$i]['item_image_id'])->set('item_images',$itemImages[$i])->set('last_updated',date('Y-m-d H:i:s'))->update('m_item_images');
    } 
  }
  else{ 
    $imagedata=[];  
    $imagedata=["item_images"=>$itemImages[$i],"item_id"=>$data['item_id'],"is_primary"=>"0","last_updated"=>date('Y-m-d H:i:s')];
    $this->db->insert('m_item_images',$imagedata);
  }
}
//=======image process end================//
//============Customization Section================//
//============use for old===================//
$oldcount=(isset($post['oldaddon_id']))?count($post['oldaddon_id']):0; 
if($oldcount!=0){
 for ($i=0; $i <count($post['oldaddon_id']) ; $i++) { 
   $oldaddon_id=$post['oldaddon_id'][$i]; 
   $id=$post['all_id'][$i];
   $this->db->where('addon_id',$post['oldaddon_id'][$i])->set('addon_name',$post['addon_name'][$i])->set('selection_type',$post["selection_type$id"])->set('max',$post["max$id"])->set('is_required',$post["required$id"])->update('m_item_addons');
   $count=count($post["oldingredienid$oldaddon_id"]);
   for ($j=0; $j < count($post["name$id"]) ; $j++) {
     if($j < $count){
       $this->db->where('ingredient_id',$post["oldingredienid$oldaddon_id"][$j])->set('name',$post["name$id"][$j])->set('price',$post["price$id"][$j])->update('m_addon_ingredients');
     }else{
      $ingredientData=["addon_id"=>$post['oldaddon_id'][$i],"name"=>$post["name$id"][$j],"price"=>$post["price$id"][$j],"currency"=>$post['currency'],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
      $this->db->insert('m_addon_ingredients',$ingredientData);
    } 
  }
}
}
//===============Use for old and new=================//
if($oldcount >0 && $post['check']=="1"){  
  $first_count=count($post['all_id']);
  if($first_count > $oldcount){
   for ($i=$oldcount; $i < $first_count ; $i++) {
    $AddonData=["item_id"=>$post['item_id'],"addon_name"=>$post['addon_name'][$i],"selection_type"=>$post["selection_type$id"],"min"=>0,"max"=>$post["max$id"],"is_required"=>$post["required$id"],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
    $this->db->insert('m_item_addons',$AddonData);
    $id="";
    $id=$post['all_id'][$i];
    $insert_id="";
    $insert_id=$this->db->insert_id();
    for ($j=0; $j <count($post["name$id"]);$j++) { 
     $ingredientData=["addon_id"=>$insert_id,"name"=>$post["name$id"][$j],"price"=>$post["price$id"][$j],"currency"=>$post['currency'],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
     $this->db->insert('m_addon_ingredients',$ingredientData);
   }
 }
}
}
//==============use for new===================//
if($post['check']=='1' && $oldcount==0){
 for ($i=0; $i <count($post['all_id']) ; $i++) {
   $id=$post['all_id'][$i];  
   $AddonData=["item_id"=>$post['item_id'],"addon_name"=>$post['addon_name'][$i],"selection_type"=>$post["selection_type$id"],"min"=>0,"max"=>$post["max$id"],"is_required"=>$post["required$id"],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
   $this->db->insert('m_item_addons',$AddonData);
   $id="";
   $id=$post['all_id'][$i];
   $insert_id="";
   $insert_id=$this->db->insert_id();
   for ($j=0; $j <count($post["name$id"]);$j++) { 
     $ingredientData=["addon_id"=>$insert_id,"name"=>$post["name$id"][$j],"price"=>$post["price$id"][$j],"currency"=>$post['currency'],"is_status"=>"1","added_on"=>date('Y-m-d H:i:s')];
     $this->db->insert('m_addon_ingredients',$ingredientData);
   }
 }
}
//===============Item insert=================//
$update=$this->db->where('item_id',$data['item_id'])->update('m_items',$data);
if($update==1){
  return 1;
}else{
  return 0;
}


}

//========deleteitem===============//
public function deleteitem($item_id){
 $update=$this->db->where('item_id',$item_id)->set('status','2')->update('m_items');
 if($update==1){
  return 1;
}else{
 return 0;
}

}
//=========item_status============//
public function item_status($data){
  $select=$this->db->where('item_id',$data['item_id'])
  ->get('m_items');
  if($select->num_rows()>0){
    $this->db->where('item_id',$data['item_id']);
    $this->db->set('status',$data['status']);          
    $update=$this->db->update('m_items');
    if($update==1){
      return 1;
    }else{
      return 0;
    }        
  }
  else{
    return 0;
  }
}


//============add_admin_address======//
public function add_admin_address($post){
 $Address=[]; 
 for ($i=0; $i < count($post['address']) ; $i++) { 
  $Address[]=["address"=>$post['address'][$i],"city"=>$post['city'][$i],"state"=>$post['state'][$i],"country"=>$post['country'][$i],"pin_code"=>$post['pincode'][$i],"a_created_id"=>$post['admin_id'],"a_created_by"=>"Admin","latitude"=>$post['latitude'][$i],"longitude"=>$post['longitude'][$i],"created_on"=>date('Y-m-d H:i:s')];
}
$this->db->insert_batch('pv_address',$Address); 
return 1; 
}

//==============add_faq_process=============//

public function add_faq_process($post){
  for ($i=0; $i <count($post['question']); $i++) {
    $data=["question"=>$post['question'][$i],"answer"=>$post['answer'][$i]];
    $this->db->insert('pv_faq',$data);
  }
  return 1; 
}

//============get_faq=================//
public function get_faq($id){
  $select=$this->db->select('id,question,answer')
  ->where('id',$id)
  ->get('pv_faq');
  if($select->num_rows()>0){
    $result=$select->row_array();
    return $result; 
  }else{
    return [];
  }                  
}

//================update_faq=============//
public function update_faq($post){
 $update=$this->db->where('id',$post['id'])->set('question',$post['question'])->set('answer',$post['answer'])->update('pv_faq');
 if($update==1) {
  return 1;
}else{
  return 0;
}
}

//============deletefaq================//
public function deletefaq($id){
  $this->db->where('id',$id)->delete('pv_faq');
  return 1;  
}
//==========check_token=============//
public function check_token($restaurant_id,$access_token){
  $select=$this->db->select('user_id')->where('user_id',$restaurant_id)->where('access_token',$access_token)
  ->get('m_user');
  if($select->num_rows()>0){
   return 1;
 }else{
  return 0;
} 
}

 //=======request_count================//
public function request_count($post){
  $where = "(o.order_status='Pending' AND ((o.payment_mode='online' AND o.payment_status NOT IN('canceled','failed','error')) OR (o.payment_mode!='online' AND o.payment_status='')))";
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('o.id,o.user_id,o.order_unique_id,u.name,o.order_type,o.order_price,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time,IFNULL((mua.address),"N/A") as address');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->join('m_user_addresses as mua','o.address_id=mua.address_id','left');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_is_status','1');
  $this->db->where($where);
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
 }
 else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
}
if($post['search'] != ''){
 $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or mua.address like '%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
 $this->db->where($searchQuery);
}
$select=$this->db->get();
$allcount =$select->num_rows();           
return $allcount; 
}

//==========get_request_list============//
public function get_request_list($limit,$start,$post){  
 $where = "(o.order_status='Pending' AND ((o.payment_mode='online' AND o.payment_status NOT IN('canceled','failed','error')) OR (o.payment_mode!='online' AND o.payment_status='')))";
 $todaydate=date('Y-m-d');
 $weekstart = date( 'Y-m-d', strtotime('monday this week'));
 $weekend = date("Y-m-d",strtotime('this week +6 days'));
 $month = date('Y-m-01');
 $year=date('Y');
 $this->db->select('o.id,o.user_id,o.address_id,o.order_unique_id,u.name,o.order_price,o.order_status,o.order_type,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time,IFNULL((select SUM(od.quantity) as quantity from m_order_details as od where od.order_id=o.id),0) as quantity,IFNULL((mua.address),"N/A") as address,IF(o.table_no != 0, o.table_no,"N/A") as table_no');
 $this->db->from('m_order as o');
 $this->db->join('m_user as u','o.user_id=u.user_id','left');
 $this->db->join('m_user_addresses as mua','o.address_id=mua.address_id','left');
 $this->db->where('o.restaurant_id',$post['restaurant_id']);
 $this->db->where('o.order_is_status','1');
 $this->db->where($where);
 $this->db->order_by('o.id','DESC');

 if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
}
else if($post['date_wise']=="2"){
 $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
 $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
}
else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
}
if($post['search'] != ''){
  $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or mua.address like '%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
  $this->db->where($searchQuery);
}
$this->db->limit($limit,$start);
$select=$this->db->get();
if($select->num_rows()>0){
 $result=$select->result_array(); 
 return $result;
}else{
 return [];
}

}

 //=======current_count================//
public function current_count($post){
  $where = "(o.order_status='Confirmed' or o.order_status = 'On the way' or o.order_status = 'Picked up' or o.order_status = 'Ready to pickup')";    
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('o.id,o.address_id,o.order_unique_id,u.name,o.order_status,o.order_type,o.order_price,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time,IFNULL((mua.address),"-") as address');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->join('m_user_addresses as mua','o.address_id=mua.address_id','left');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_is_status','1');
  $this->db->where($where);
  $this->db->where_not_in('o.payment_status','canceled');
  $this->db->where_not_in('o.payment_status','failed');
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
  }
  if($post['search'] != ''){
   $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or mua.address like '%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $select=$this->db->get();
 $allcount =$select->num_rows();           
 return $allcount; 
}

//==========get_current_list============//
public function get_current_list($limit,$start,$post){
  $where = "(o.order_status='Confirmed' or o.order_status = 'On the way' or o.order_status = 'Picked up' or o.order_status = 'Ready to pickup')";    
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('o.id,o.user_id,o.order_unique_id,u.name,o.order_status,o.order_price,o.order_type,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time,IFNULL((select SUM(od.quantity) as quantity from m_order_details as od where od.order_id=o.id),0) as quantity,IFNULL((mua.address),"N/A") as address,IF(o.table_no != 0, o.table_no,"N/A") as table_no');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->join('m_user_addresses as mua','o.address_id=mua.address_id','left');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_is_status','1');
  $this->db->where($where);
  $this->db->where_not_in('o.payment_status','canceled');
  $this->db->where_not_in('o.payment_status','failed');
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
  }
  if($post['search'] != ''){
    $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or mua.address like '%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
    $this->db->where($searchQuery);
  }
  $this->db->limit($limit,$start);
  $select=$this->db->get();
  if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }

}


 //=======history count================//
public function history_count($post){
 // $where = "(o.order_status='Delivered' or o.order_status = 'Cancel' or o.payment_status='canceled' or o.payment_status='failed')"; 
  $where = "((o.order_status='Delivered' or o.order_status = 'Cancel' or o.payment_status='canceled' or o.payment_status='failed' or o.payment_status='error') OR (o.payment_status = '' AND payment_mode = 'online'))"; 
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('o.id,o.user_id,o.order_unique_id,u.name,o.order_type,o.order_price,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_is_status','1');
  $this->db->where($where);
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
  }
  if($post['search'] != ''){
   $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
   $this->db->where($searchQuery);
 }
 $select=$this->db->get();
 $allcount =$select->num_rows();           
 return $allcount; 
}

//==========get_history_list============//
public function get_history_list($limit,$start,$post){
 // $where = "(o.order_status='Delivered' or o.order_status = 'Cancel' or o.payment_status='canceled' or o.payment_status='failed')"; 
  $where = "((o.order_status='Delivered' or o.order_status = 'Cancel' or o.payment_status='canceled' or o.payment_status='failed' or o.payment_status='error') OR (o.payment_status = '' AND payment_mode = 'online'))"; 
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->select('o.id,o.user_id,o.order_unique_id,u.name,o.order_type,o.order_price,DATE_FORMAT(o.added_on,"%d %b %Y") as order_date,DATE_FORMAT(o.added_on,"%h:%i") as order_time,IFNULL((select SUM(od.quantity) as quantity from m_order_details as od where od.order_id=o.id),0) as quantity,(CASE WHEN (o.payment_mode="online" AND o.payment_status != "success") THEN o.payment_status WHEN (o.payment_mode="online" AND o.payment_status = "") THEN "failed" ELSE o.order_status END) as order_status');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_is_status','1');
  $this->db->where($where);
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(o.added_on, "%Y")=',$year);
  }
  if($post['search'] != ''){
    $searchQuery="(u.name like '%".$post['search']."%' or o.order_type like '%".$post['search']."%' or o.order_unique_id like'%".$post['search']."%' or o.order_status like'%".$post['search']."%')";
    $this->db->where($searchQuery);
  }
  $this->db->limit($limit,$start);
  $select=$this->db->get();
  if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }
}

//=================order_user===========//
public function order_user($post){
  $where = "((order_status = 'Cancel' or payment_status!='success') OR (payment_status = '' AND payment_mode = 'online'))"; 
  $todaydate=date('Y-m-d');
  $weekstart = date( 'Y-m-d', strtotime('monday this week'));
  $weekend = date("Y-m-d",strtotime('this week +6 days'));
  $month = date('Y-m-01');
  $year=date('Y');
  $this->db->from("m_order");
  $this->db->where('restaurant_id',$post['restaurant_id']);
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(added_on, "%Y")=',$year);
  }
  $total_order=$this->db->count_all_results();

  $this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price');
  $this->db->from("m_order");
  $this->db->where('restaurant_id',$post['restaurant_id']);
  $this->db->where('order_status','Delivered');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart);
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend);
  }
  else if($post['date_wise']=="3"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month);
  }else{
    $this->db->where('DATE_FORMAT(added_on, "%Y")>=',$year);
  }
  $select=$this->db->get();
  $total_earning=$select->row_array();

  $this->db->from("m_order");
  $this->db->where('restaurant_id',$post['restaurant_id']);
  $this->db->where($where);
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
  }
  else if($post['date_wise']=="2"){
   $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart);
   $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend);
 }
 else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(added_on, "%Y")>=',$year);
}
$cancel_order=$this->db->count_all_results();

$this->db->from("m_order");
$this->db->where('restaurant_id',$post['restaurant_id']);
if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}
else if($post['date_wise']=="2"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$weekstart);
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',$weekend);
}
else if($post['date_wise']=="3"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',$month);
}else{
  $this->db->where('DATE_FORMAT(added_on, "%Y")>=',$year);
}
$this->db->where('order_status','Delivered');
$total_delivery=$this->db->count_all_results();

$result=["total_order"=>$total_order,"total_earning"=>$total_earning['order_price'],"cancel_order"=>$cancel_order,"total_delivery"=>$total_delivery];
return $result;
}

//=======order_confirmed================//
public function order_confirmed($id){
  $update=$this->db->where('id',$id)->set('order_status','Confirmed')->update('m_order');
  if($update==1){
    return 1;
  }else{
    return 0;
  }
}

//==========order_status=============//
public function order_status($id,$order_status){
  $update=$this->db->where('id',$id)->set('order_status',$order_status)->update('m_order');
  if($update==1){
    return 1;
  }else{
    return 0;
  }
}

//=========order_accept===============//
public function order_accept($id,$pdf_name){
  $update=$this->db->where('id',$id)->set('order_status','Delivered')->set('invoice_url',$pdf_name)->update('m_order');
  if($update==1){
    return 1;
  }else{
    return 0;
  }
}

//==========order_detail================//
public function order_detail($id){
  $this->db->select('o.id,o.order_unique_id,o.table_no,o.guests,o.currency,o.order_price,o.payment_mode,o.discount,o.coupon_code,o.order_type,o.instructions,IFNULL((o.vehicle_color),"N/A") as vehicle_color,IFNULL((o.vehicle_name),"N/A") as vehicle_name,IFNULL((o.vehicle_no),"N/A") as vehicle_no,IFNULL((o.vehicle_type),"N/A") as vehicle_type,IFNULL((Select CONCAT(address, ",", your_location, ",", landmark) from m_user_addresses mua where mua.address_id=o.address_id),"N/A") as address,IFNULL((c.coupon_name),"") as coupon_name,u.country_code,u.mobile_no,o.wallet_amount,o.ambassador_amount');
  $this->db->from('m_order as o');
  $this->db->join('m_coupons as c','o.coupon_code=c.id','LEFT');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->where('o.id',$id);
  $select=$this->db->get();
  $result=$select->row_array();
  $this->db->select('od.currency,od.item_price,od.quantity,i.item_name,od.ingredient_name');
  $this->db->from('m_order_details as od');
  $this->db->join('m_items as i','od.item_id=i.item_id','LEFT');
  $this->db->where('order_id',$id);
  $select1=$this->db->get();
  $data=$select1->result_array();
  $totalQuantity=0;
  for ($i=0; $i < count($data); $i++) { 
   $totalQuantity+=$data[$i]['quantity'];
 }
 if($result['vehicle_color']!=""){
  if($result['vehicle_color']=="#FF0000"){
   $result['vehicle_color']="Red"; 
 }
 if($result['vehicle_color']=="#000000"){
   $result['vehicle_color']="Black"; 
 }
 if($result['vehicle_color']=="#FFFFFF"){
   $result['vehicle_color']="White"; 
 }
 if($result['vehicle_color']=="#A9A9A9"){
   $result['vehicle_color']="Grey"; 
 }  
}
$result['totalQuantity']=$totalQuantity;
$result['data']=$data;
return $result;
}

//==========assign_confirmed================//
public function assign_confirmed($address_id){
 $newData=[]; 
 $where = "(order_status = 'On the way' or order_status = 'Picked up')"; 
 $this->db->select('address');
 $this->db->where('address_id',$address_id);
 $select=$this->db->get('m_user_addresses');
 $result=$select->row_array();
 $this->db->select('user_id,name');
 $this->db->from('m_user');
 $this->db->where('user_type','driver');
 $select1=$this->db->get();
 $data=$select1->result_array();
 for ($i=0; $i <count($data) ; $i++) {
  $this->db->select('id');
  $this->db->where('delivery_agent_id',$data[$i]['user_id']);
  $this->db->where($where);
  $query=$this->db->get('m_order');
  if($query->num_rows()==0){
   $newData[]=$data[$i]; 
 } 
}
$result['driver']=$newData;
return $result;
}

//=========InvoiceData================//
public function InvoiceData($id){
  $this->db->select('o.order_unique_id,o.added_on,o.order_price,o.currency,u.name,u.email_id,coupon_code,o.discount,o.wallet_amount,o.ambassador_amount,(select SUM(od.quantity*od.item_price) from m_order_details as od where od.order_id='.$id.') as sub_total');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->where('id',$id);
  $select=$this->db->get();
  $result=$select->row_array();
  if($result['coupon_code']!='0'){
    $this->db->select('coupon_name,coupon_value');
    $this->db->where('id',$result['coupon_code']);
    $query=$this->db->get('m_coupons');
    $coupondata=$query->row_array();
    $result['coupondata']=$coupondata;  
  }else{
    $result['coupondata']=[];  
  }
  $this->db->select('od.currency,od.item_price,od.quantity,i.item_name,IFNULL((v.variant_name),"") as variant_name,');
  $this->db->from('m_order_details as od');
  $this->db->join('m_items as i','od.item_id=i.item_id','LEFT');
  $this->db->join('m_variants as v','od.variant_id=v.variant_id','LEFT');
  $this->db->where('order_id',$id);;
  $select1=$this->db->get();
  $data=$select1->result_array();
  $result['orderData']=$data;
  return $result;
}


//===========all_reason=================//
public function all_reason(){
  $select=$this->db->select('reason_id,reason_title')->get('m_allreason');
  if($select->num_rows()>0){
    $result=$select->result_array();
    return $result; 
  }else{
    return [];
  }
}

//==============order_cancel============//
public function order_cancel($data){
  $restaurantData=$this->session->userdata('restaurantData');  
  $this->db->select('u.user_id,o.wallet_amount,u.user_device_token,u.user_device_type,u.allow_notification');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','left');
  $this->db->where('o.id',$data['id']);
  $select=$this->db->get();
  $result=$select->row_array();
  if($result['wallet_amount'] > 0){
    $price=$result['wallet_amount'];  
    $this->db->set('user_wallet', "user_wallet+".$price, FALSE);
    $this->db->where('user_id',$result['user_id']);
    $this->db->update('m_user');
    $walletData=["user_id"=>$result['user_id'],"amount"=>$result['wallet_amount'],"order_id"=>"","status"=>"credit"];
    $this->db->insert('m_wallet_history',$walletData);
    $NotiData=["title"=>"Order Cancelled","body"=>"Your order has been cancelled . The wallet amount has been credited back to your account","order_type"=>"wallet"];
    if($result['user_device_token'] != '' && $result['allow_notification']=="1") {
      $common=new Common();
      $common->notification($result['user_device_token'],$result['user_device_type'],$NotiData,"wallet");
    }
    $NotificationData=['sender_id'=>$restaurantData['restaurant_id'],'receiver_id' =>$result['user_id'],'title' =>$NotiData['title'],'message'=>$NotiData['body'],'type'=>"wallet","sender_type"=>'restaurant',"addded_on"=>date('Y-m-d H:i:s')];
    $this->db->insert('m_notifications',$NotificationData);
  }
  $update=$this->db->where('id',$data['id'])->update('m_order',$data);
  if($update==1){
    return 1;
  }else{
    return 0;
  }  
}

//========order_assign=============//
public function order_assign($data){
 $update=$this->db->where('id',$data['id'])->update('m_order',$data);
 if($update==1){
  return 1;
}else{
  return 0;
}  
}

 //=======report_count================//
public function report_count($post){
 $todaydate=date('Y-m-d');
 $month = date('Y-m-01');
 $year=date('Y');
 $this->db->select('o.id,o.order_unique_id,o.currency,o.order_price');
 $this->db->from('m_order as o');
 $this->db->where('o.restaurant_id',$post['restaurant_id']);
 $this->db->where('o.order_status','Delivered');
 $this->db->order_by('o.id','DESC');
 if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
}
else{
 $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
 $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
}
$select=$this->db->get();
$allcount =$select->num_rows();           
return $allcount; 
}

//==========get_report_list============//
public function get_report_list($limit,$start,$post){
  $todaydate=date('Y-m-d');
  $this->db->select('o.id,o.order_unique_id,o.currency,o.wallet_amount,o.order_price,o.payment_mode,IFNULL((select SUM(od.quantity) as quantity from m_order_details as od where od.order_id=o.id),0) as quantity,o.ambassador_amount');
  $this->db->from('m_order as o');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_status','Delivered');
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else{
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
 }
 $this->db->limit($limit,$start);
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }
}

//=================get_report_header=================//
public function get_report_header($post){
  $todaydate=date('Y-m-d');
  $year=date('Y');
  $this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price');
  $this->db->from("m_order");
  $this->db->where('restaurant_id',$post['restaurant_id']);
  $this->db->where('order_status','Delivered');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
  }else{
   $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
   $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
 }
 $select=$this->db->get();
 $total_earning=$select->row_array();

 $this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price');
 $this->db->from("m_order");
 $this->db->where('restaurant_id',$post['restaurant_id']);
 $this->db->where('order_status','Delivered');
 $this->db->where('payment_mode','online');
 if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}else{
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
}
$select1=$this->db->get();
$online_payment=$select1->row_array();

$this->db->select('IFNULL((ROUND(SUM(order_price), 2)),0) as order_price');
$this->db->from("m_order");
$this->db->where('restaurant_id',$post['restaurant_id']);
$this->db->where('order_status','Delivered');
$this->db->where('payment_mode','cod');
if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}else{
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
}
$select2=$this->db->get();
$cod=$select2->row_array();


$this->db->select('IFNULL((ROUND(SUM(wallet_amount), 2)),0) as wallet_amount');
$this->db->from("m_order");
$this->db->where('restaurant_id',$post['restaurant_id']);
$this->db->where('order_status','Delivered');
if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}else{
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
}
$select3=$this->db->get();
$wallet=$select3->row_array();


$this->db->select('IFNULL((ROUND(SUM(ambassador_amount), 2)),0) as ambassador_amount');
$this->db->from("m_order");
$this->db->where('restaurant_id',$post['restaurant_id']);
$this->db->where('order_status','Delivered');
if($post['date_wise']=="1"){
  $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")=',$todaydate);
}else{
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
 $this->db->where('DATE_FORMAT(added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
}
$select4=$this->db->get();
$ambassador=$select4->row_array();

$result=["total_earning"=>$total_earning['order_price'],"online_payment"=>$online_payment['order_price'],"cod"=>$cod['order_price'],"wallet"=>$wallet['wallet_amount'],"ambassador"=>$ambassador['ambassador_amount']];
return $result; 
}

//==========report_export============//
public function report_export($post){
  $todaydate=date('Y-m-d');
  $this->db->select('o.id,o.order_unique_id,o.currency,o.order_price,o.wallet_amount,o.payment_mode,IFNULL((select SUM(od.quantity) from m_order_details as od where od.order_id=o.id),0) as quantity,o.ambassador_amount');
  $this->db->from('m_order as o');
  $this->db->where('o.restaurant_id',$post['restaurant_id']);
  $this->db->where('o.order_status','Delivered');
  $this->db->order_by('o.id','DESC');
  if($post['date_wise']=="1"){
    $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")=',$todaydate);
  }
  else{
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")>=',date('Y-m-d',strtotime($post['from_date'])));
   $this->db->where('DATE_FORMAT(o.added_on, "%Y-%m-%d")<=',date('Y-m-d',strtotime($post['to_date'])));
 }
 $select=$this->db->get();
 if($select->num_rows()>0){
   $result=$select->result_array(); 
   return $result;
 }else{
   return [];
 }
}
//==========order_list==================//
public function order_list($restaurant_id){
  $select=$this->db->select('delivery_status,dine_in_status,take_away_status')->where('restaurant_id',$restaurant_id)->get('m_vendors');
  $result=$select->row_array();
  return $result;

}

//==========order_type_status====================//
public function order_type_status($post){
  if($post['order_type']=="delivery"){
   $this->db->where('restaurant_id',$post['restaurant_id'])->set('delivery_status',$post['status'])->update('m_vendors');
   return 1; 
 } 
 else if($post['order_type']=="take_away"){
  $this->db->where('restaurant_id',$post['restaurant_id'])->set('take_away_status',$post['status'])->update('m_vendors');
  return 1;
}else{
  $this->db->where('restaurant_id',$post['restaurant_id'])->set('dine_in_status',$post['status'])->update('m_vendors');
  return 1;

} 
}

//===========Order Notification=========================//

public function order_notification($order_id,$order_status){
  $NotiData=[];
  $restaurantData=$this->session->userdata('restaurantData');  
  $this->db->select('u.user_id,u.name,u.user_device_token,u.user_device_type,u.allow_notification,o.order_type,o.order_unique_id,o.delivery_agent_id');
  $this->db->from('m_order as o');
  $this->db->join('m_user as u','o.user_id=u.user_id','LEFT');
  $this->db->where('o.id',$order_id);
  $select=$this->db->get();
  $result=$select->row_array();
  if($order_status=='confirm'){
    $notitype="confirmed";
    $title='Order confirm';
    $body='Your order '.$result['order_unique_id'].' is confirmed';  
  }else if($order_status=='On the way'){
    $notitype="dispatched";
    $title="Order Dispatched";
    $body='Your order '.$result['order_unique_id'].' is dispatched';  
  }
  else if($order_status=='Ready to pickup'){
    $notitype="ready to pickup";
    $title="Your order is ready";
    $body='Your order '.$result['order_unique_id'].' is ready to pickup';  
  }
  else if($order_status=='delivered'){
    $notitype="delivered";
    $title="Order Delivered";
    $body='Your order '.$result['order_unique_id'].' is delivered'; 
    $NotiData['restaurant_id']=$restaurantData['restaurant_id'];
    $NotiData['delivery_agent_id']=$result['delivery_agent_id'];
  } 
  else if($order_status=='Picked up'){
    $notitype="picked up";
    $title="Order Picked Up";
    $body='Your order '.$result['order_unique_id'].' is ready to pick up.';  
  }  
  else if($order_status=='cancel'){
    $notitype="cancelled";
    $title='Order Cancelled';
    $body='Your order '.$result['order_unique_id'].' is cancelled';      
  }
  if($result['user_device_token'] != '' && $result['allow_notification']=="1") {
    $common=new Common();
    $NotiData['title']=$title;
    $NotiData['body']=$body;
    $NotiData['order_type']=$result['order_type'];
    $NotiData['order_id']=$order_id;
    $common->notification($result['user_device_token'],$result['user_device_type'],$NotiData,$notitype);
  }
  $NotificationData=['sender_id'=>$restaurantData['restaurant_id'],'receiver_id' =>$result['user_id'],'title' =>$title,'message'=>$body,'type'=>$notitype,"sender_type"=>'restaurant',"addded_on"=>date('Y-m-d H:i:s'),"order_id"=>$order_id];
  $this->db->insert('m_notifications',$NotificationData);
  return 1;
}

//============driver_notification================//

public function driver_notification($id,$driver_id){
  $restaurantData=$this->session->userdata('restaurantData');  
  $notitype="assign";
  $query=$this->db->select('order_unique_id')->where('id',$id)->get('m_order');
  $res=$query->row_array();
  $select=$this->db->select('user_id,user_device_token,user_device_type,allow_notification')->where('user_id',$driver_id)->where('status','1')->get('m_user');
  $result=$select->row_array();
  if($result['user_device_token'] != '' && $result['allow_notification']=="1") {
    $NotiData=["title"=>"Order Assign","body"=>$res['order_unique_id']." has been assign you.","order_type"=>$notitype,"order_id"=>$id];
    $common=new Common();
    $res=$common->notification($result['user_device_token'],$result['user_device_type'],$NotiData,$notitype);
  }
  $NotificationData=['sender_id'=>$restaurantData['restaurant_id'],'receiver_id' =>$result['user_id'],'title' =>$NotiData['title'],'message'=>$NotiData['body'],'type'=>$notitype,"sender_type"=>'restaurant',"addded_on"=>date('Y-m-d H:i:s')];
  $this->db->insert('m_notifications',$NotificationData);
  return 1; 
}
//=======customization=================//
public function customization($item_id){
  $this->db->select('addon_id,addon_name,selection_type,min,max,is_required,is_status');
  $this->db->where('item_id',$item_id);
  $this->db->where_not_in('is_status','2');
  $select=$this->db->get('m_item_addons');
  if($select->num_rows()>0){
    $result=$select->result_array();  
    foreach ($result as $key => $value) {
      $this->db->select('ingredient_id,name,price,is_status');
      $this->db->where('addon_id',$value['addon_id']);
      $this->db->where_not_in('is_status','2');
      $query=$this->db->get('m_addon_ingredients');
      $result[$key]['ingredientData']=$query->result_array();
    }
    return $result;
  }else{
    return [];
  }  
}

//============change_ingredient_status=============//
public function change_ingredient_status($post){
  $this->db->where('ingredient_id',$post['id'])->set('is_status',$post['status'])->update('m_addon_ingredients');
  return 1;

}

}//========class=======//
