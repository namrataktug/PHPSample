<?php
	defined('BASEPATH') or exit('No direct script access allowed.');
	class SterklaModel extends CI_Model
	{



		// =================== Upload on S3 comman function ==================//

		public function uploadS3($name, $tmp_name)
		{
			$fileName = $name;
			$fileTempName = $tmp_name;
			if ($this->s3->putObjectFile($fileTempName, "sterklastazingimages", $fileName, S3::ACL_PUBLIC_READ)) {
				return $fileName;
			} else {
				echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				die();
			}
		}


		/* ==================== Check Access Key  =================== */

		function checkAssessKey($access_key)
		{
			$query = $this->db->where('access_key', $access_key)->get('users');

			if ($query->num_rows() > 0) {
				return 1;
			} else {
				return 0;
			}
		}

	/* ==================== Check Coach Status  =================== */

		function checkCoach($user_id)
		{
			$query = $this->db->where('user_id', $user_id)
				->where('coach_status', 'active')
				->get('users');

			if ($query->num_rows() > 0) {
				return 1;
			} else {
				return 0;
			}
		}


        /* ==================== Check Member Status  =================== */

		function checkMember($user_id)
		{
			$query = $this->db->where('user_id', $user_id)
				->where('member_status', 'active')
				->get('users');

			if ($query->num_rows() > 0) {
				return 1;
			} else {
				return 0;
			}
		}


        function makePayment($array)
		{

			if (!isset($array['current_time']))
				$array['current_time'] = date('Y-m-d H:i:s');

			$random =  md5(rand(10000, 99999));
			$ramdom_no = strtoupper(substr($random, 0, 8));
			$data = [
				'user_id' => $array['user_id'],
				'payee_id' => $array['coachId'],
				'order_no' => $ramdom_no,
				'amount' => $array['amount'],
				'paypal_trans_id' => $array['paypal_trans_id'],
				'transaction_type' => $array['transaction_type'],
				'created_on' => $array['current_time']
			];
			$this->db->insert('transaction', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			} else {
				return false;
			}
		}


        //--------------getCategory-----------------------------
		public function getWebCoachSubCategory()
		{
			$query = $this->db->order_by("category_name", "asc")->order_by('category_name', 'asc')->get('category');
			if ($query->num_rows() > 0) {
				$result = $query->result_array();
				$i = 0;
				foreach ($result as $res) {
					$query = $this->db->order_by("category_name", "asc")->where('category_id', $res['category_id'])->get('sub_category');
					$subCategory = $query->result_array();
					$result[$i]['sub_category'] = $subCategory;
					$i++;
				}
				return $result;
			} else {
				return [];
			}
		}


        /* ==================== Get Personality =================== */
		function getPersonality()
		{

			$result = array();
			$query = $this->db->get('personality_category');
			$personality_result = $query->result_array();
			$result['personality'] = $personality_result;
			$query = $this->db->where('type', 'coach')->order_by('category_name', 'asc')->get('category');
			$category_result = $query->result_array();
			$result['category'] = $category_result;
			return $result;
		}



        	/* ==================== Function =================== */
		function dashboard($post)
		{

			date_default_timezone_set('UTC');

			$allResult = array();
			$allResult = array(
				'rating' => '0',
				'sessions' => '0',
				'content_pack' => '0',
				'cp_earning' => '0.00',
				'sessions_earning' => '0.00',
				'followers' => '0',
				'next_payout' => '0',
				'minutes_coached' => '0',
				'member_coached' => '0',
				'level' => '',
				'level_description' => '',
				'level_image' => ''
			);


			$refers = $this->coach_refers();
			$ref_percentage = $refers['ref_percentage'] - $refers['non_ref_percentage'];

			/*Coach Ratings*/
			$query = $this->db->select('IFNULL((SELECT  CAST(sum(rate)/count(id) as DECIMAL(11,2)) FROM `coach_ratings` as `cr` where cr.coach_id=u.user_id),"0") as total_rating')
				->where('u.user_id', $post['user_id'])->from('users as u')
				->get();
			$ratingData = $query->row_array();
			$allResult['rating'] = $ratingData['total_rating'];


			$where = 'YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE)';

			/*Coach sessions*/
			$sessionsQuery = $this->db->where('coach_id', $post['user_id'])
				->where('appointment_status', 'Accept')
				->where($where)
				->where('concat(appointment_date, " ", appointment_end_time) < NOW()')
				->get('appointment');

			$allResult['sessions'] = 0;
			$allResult['sessions'] = (string) $sessionsQuery->num_rows();


			$CPQuery =	$this->db->select('count(m.id) content_pack_totals')
				->join('content_pack as cp', 'm.cp_id=cp.cp_id')
				->where('created_by', $post['user_id'])
				->where('m.view_type', 'purchased')
				->where('m.payment_status="remaining" and YEAR(m.purchase_date)= YEAR(CURRENT_DATE) and MONTH(m.purchase_date)=MONTH(CURRENT_DATE)')
				->group_by('created_by')
				->get('my_packs as m');

			if ($CPQuery->num_rows() > 0)
				$allResult['content_pack'] = (string) $CPQuery->row_array()['content_pack_totals'];
			// $next_payout = '0.00';

			/*Session Earning*/

			$query = $this->db->select('
			
			IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and payment_status="remaining" and appointment_type ="affiliate_course" and coach_id=u.user_id group by coach_id) as decimal(11,2)),0)+IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and payment_status="remaining" and coach_commission="yes" and appointment_type ="affiliate_course" and coach_id=u.user_id group by coach_id)  as decimal(11,2)),0)+IFNULL(CAST((select sum(appointment_rate) from appointment where (appointment_status="Accept" or appointment_status="Reject_Request") and concat(appointment_date, " " ,appointment_end_time) < NOW() and payment_status="remaining" and appointment_type = "affiliate_course" and   YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and coach_id=u.user_id group by coach_id)  as decimal(11,2)),0) + IFNULL(CAST((select sum(appointment_rate) from appointment where coach_commission="yes" and  payment_status="remaining" and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and  coach_id=u.user_id and (appointment_status="Accept" or appointment_status="Reject_Request") and appointment_type="affiliate_course" and concat(appointment_date, " " ,appointment_end_time) < NOW() group by coach_id) as decimal(11,2)),0)

			+
			
			IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and payment_status="remaining" and appointment_type !="affiliate_course" and coach_id=u.user_id group by coach_id)/100*' . $refers['non_ref_percentage'] . ' as decimal(11,2)),0)+IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and payment_status="remaining" and coach_commission="yes" and appointment_type !="affiliate_course" and coach_id=u.user_id group by coach_id)/100*' . $ref_percentage . ' as decimal(11,2)),0)+IFNULL(CAST((select sum(appointment_rate) from appointment where appointment_status="Accept"  and concat(appointment_date, " " ,appointment_end_time) < NOW() and payment_status="remaining" and appointment_type != "affiliate_course" and   YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and coach_id=u.user_id group by coach_id)/100*' . $refers['non_ref_percentage'] . ' as decimal(11,2)),0) + IFNULL(CAST((select sum(appointment_rate) from appointment where coach_commission="yes" and  payment_status="remaining" and YEAR(appointment_date)= YEAR(CURRENT_DATE) and MONTH(appointment_date)=MONTH(CURRENT_DATE) and  coach_id=u.user_id and appointment_status="Accept" and appointment_type!="affiliate_course" and concat(appointment_date, " " ,appointment_end_time) < NOW() group by coach_id)/100*' . $ref_percentage . ' as decimal(11,2)),0)  as payAmount')
				->where('u.is_verified', 'yes')
				->where('u.coach_refer_code !=', '')
				->where('u.user_id', $post['user_id'])
				->get('users as u');


			if ($query->num_rows() > 0) {
				$SessionResult = $query->row_array();
				$allResult['sessions_earning'] = (string) $SessionResult['payAmount'];
			}



			/*CP Earning*/
			$cp_earning = "0";
			$query = $this->db->select('m.*')
				->join('content_pack as cp', 'm.cp_id=cp.cp_id')
				->where('created_by', $post['user_id'])
				->where('m.view_type', 'purchased')
				->where('m.price !=', '0')
				->where('m.payment_status="remaining" and YEAR(m.purchase_date)= YEAR(CURRENT_DATE) and MONTH(m.purchase_date)=MONTH(CURRENT_DATE)')
				->get('my_packs as m');
			if ($query->num_rows() > 0) {
				$cpEarning = $query->result_array();
				foreach ($cpEarning as $key => $value) {
					// Installment Transaction push in array
					if ($cpEarning[$key]['purchase_type'] == 'installment') {
						$cpInstallmentEarning = $cpEarning;
						$query = $this->db->select('content_pack_installment.*')
							->where('my_pack_id', $cpEarning[$key]['id'])
							->get('content_pack_installment');
						if ($query->num_rows() > 0) {
							$result = $query->result_array();
							foreach ($result as $key1 => $value1) {
								if ($value['coach_commission'] == 'yes') {
									$cpInstallmentEarning[0]['amount'] = ($value1['installment_amount'] / 100) * $refers['ref_percentage'];
								} else {
									$cpInstallmentEarning[0]['amount'] = ($value1['installment_amount'] / 100) * $refers['non_ref_percentage'];
								}

								$cp_earning = $cp_earning + $cpInstallmentEarning[0]['amount'];
							}
						}
					} else {
						if ($value['coach_commission'] == 'yes') {
							$cpEarning[$key]['amount'] = ($value['price'] / 100) * $refers['ref_percentage'];
						} else {
							$cpEarning[$key]['amount'] = ($value['price'] / 100) * $refers['non_ref_percentage'];
						}
						$cp_earning = $cp_earning + $cpEarning[$key]['amount'];
					}
				}
			}

			$cp_earning = number_format((float)$cp_earning, 2, '.', '');

			$allResult['cp_earning'] = (string)$cp_earning;

			//Total Earning Start 
			$cp_total_earning = "0";
			$query = $this->db->select('m.*')
				->join('content_pack as cp', 'm.cp_id=cp.cp_id')
				->where('created_by', $post['user_id'])
				->where('m.view_type', 'purchased')
				->where('m.payment_status', 'remaining')
				->where('m.price !=', '0')
				->get('my_packs as m');
			if ($query->num_rows() > 0) {
				$cpEarning = $query->result_array();
				foreach ($cpEarning as $key => $value) {
					// Installment Transaction push in array
					if ($cpEarning[$key]['purchase_type'] == 'installment') {
						$cpInstallmentEarning = $cpEarning;
						$query = $this->db->select('content_pack_installment.*')
							->where('my_pack_id', $cpEarning[$key]['id'])
							->get('content_pack_installment');
						if ($query->num_rows() > 0) {
							$result = $query->result_array();
							foreach ($result as $key1 => $value1) {
								if ($value['coach_commission'] == 'yes') {
									$cpInstallmentEarning[0]['amount'] = ($value1['installment_amount'] / 100) * $refers['ref_percentage'];
								} else {
									$cpInstallmentEarning[0]['amount'] = ($value1['installment_amount'] / 100) * $refers['non_ref_percentage'];
								}

								$cp_total_earning = $cp_total_earning + $cpInstallmentEarning[0]['amount'];
							}
						}
					} else { // Normal Transaction push in array
						if ($value['coach_commission'] == 'yes') {
							$cpEarning[$key]['amount'] = ($value['price'] / 100) * $refers['ref_percentage'];
						} else {
							$cpEarning[$key]['amount'] = ($value['price'] / 100) * $refers['non_ref_percentage'];
						}

						$cp_total_earning = $cp_total_earning + $cpEarning[$key]['amount'];
					}
				}
			}

			$query = $this->db->select('


			IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and payment_status="remaining" and appointment_type="affiliate_course" and coach_id=u.user_id group by coach_id) as decimal(11,2)),0)+IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and payment_status="remaining" and appointment_type="affiliate_course" and coach_commission="yes" and coach_id=u.user_id group by coach_id) as decimal(11,2)),0)+IFNULL(CAST((select sum(appointment_rate) from appointment where (appointment_status="Accept" or appointment_status="Reject_Request") and concat(appointment_date, " " ,appointment_end_time) < NOW() and payment_status="remaining" and appointment_type="affiliate_course" and coach_id=u.user_id group by coach_id) as decimal(11,2)),0) + IFNULL(CAST((select sum(appointment_rate) from appointment where coach_commission="yes" and  payment_status="remaining" and appointment_type="affiliate_course" and   coach_id=u.user_id and (appointment_status="Accept" or appointment_status="Reject_Request") and concat(appointment_date, " " ,appointment_end_time) < NOW() group by coach_id) as decimal(11,2)),0)

			+
			
			IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and payment_status="remaining" and appointment_type!="affiliate_course" and coach_id=u.user_id group by coach_id)/100*' . $refers['non_ref_percentage'] . ' as decimal(11,2)),0)+IFNULL(CAST((select sum(refund_amount) from appointment where appointment_status="Cancel" and refund_amount!=0.00 and payment_status="remaining" and appointment_type!="affiliate_course" and coach_commission="yes" and coach_id=u.user_id group by coach_id)/100*' . $ref_percentage . ' as decimal(11,2)),0)+IFNULL(CAST((select sum(appointment_rate) from appointment where appointment_status="Accept" and concat(appointment_date, " " ,appointment_end_time) < NOW() and payment_status="remaining" and appointment_type!="affiliate_course" and coach_id=u.user_id group by coach_id)/100*' . $refers['non_ref_percentage'] . ' as decimal(11,2)),0) + IFNULL(CAST((select sum(appointment_rate) from appointment where coach_commission="yes" and  payment_status="remaining" and appointment_type!="affiliate_course" and   coach_id=u.user_id and appointment_status="Accept" and concat(appointment_date, " " ,appointment_end_time) < NOW() group by coach_id)/100*' . $ref_percentage . ' as decimal(11,2)),0)  as payAmount')->where('u.user_id', $post['user_id'])
				->get('users as u');


			if ($query->num_rows() > 0) {
				$session_total_earning = $query->row_array();
				$allResult['next_payout'] = $cp_total_earning + $session_total_earning['payAmount'];
				$allResult['next_payout'] =	number_format((float)$allResult['next_payout'], 2, '.', '');
			}
			$allResult['next_payout'] = (string)($allResult['next_payout']);

			// TOTAL EARNING END

			/*My Followers*/

			$followerQuery = $this->db->select('count(following_id) as followers')->where('following_id', $post['user_id'])
				->get('followers');

			$allResult['followers'] = (string) $followerQuery->row_array()['followers'];

			/*Minutes Coached and member coached*/
			$this->db->select('u.user_id')->distinct()
				->from('appointment as ap')
				->join('users as u', 'u.user_id=ap.user_id', 'left')
				->where('ap.coach_id', $post['user_id'])
				->where('ap.appointment_status', 'Accept')
				->where('concat(ap.appointment_date, " ", ap.appointment_end_time) < NOW()')
				->where_not_in('u.member_status', 'inactive')
				->order_by('ap.id', 'DESC')
				->group_by('ap.user_id');

			$appointment_clients = $this->db->get_compiled_select();

			$this->db->select('u.user_id')->distinct()
				->from('my_packs mp')
				->join('content_pack as cp', 'cp.cp_id=mp.cp_id')
				->join('users as u', 'u.user_id=mp.user_id')
				->where('cp.created_by', $post['user_id'])
				->group_by('mp.user_id');
			$course_clients = $this->db->get_compiled_select();
			$query = '(' . $appointment_clients . ') UNION (' . $course_clients . ') ';
			$memberCoachedQuery = $this->db->query($query);


			$allResult['member_coached'] =	(string)$memberCoachedQuery->num_rows();

			$minutesCoachedQuery = $this->db->select('SUM(appointment_duration) as minutes_coached')->where('coach_id', $post['user_id'])
				->where('appointment_status', 'Accept')
				->where('concat(appointment_date, " ", appointment_end_time) < NOW()')
				->group_by('coach_id')
				->get('appointment');

			if ($minutesCoachedQuery->num_rows() > 0) {
				$minutesCoachedResult = $minutesCoachedQuery->row_array();
				$allResult['minutes_coached'] = $minutesCoachedResult['minutes_coached'];
			}


			/*Level And Descrition*/

			$this->db->set('coaching_minutes', $allResult['minutes_coached'])
				->where('user_id', $post['user_id'])
				->update('coach_details');

			$levelResultquery = $this->db->where('level_time <=', $allResult['minutes_coached'])
				->limit('1')
				->order_by('level_time', 'DESC')
				->where('level_user', 'Coach')
				->get('levels');
			if ($levelResultquery->num_rows() > 0) {
				$levelResult = $levelResultquery->result_array();
				$this->db->set('level', $levelResult[0]['level_name'])
					->set('level_image', $levelResult[0]['level_image'])
					->set('coaching_minutes', $allResult['minutes_coached'])
					->where('user_id', $post['user_id'])
					->update('coach_details');
			}

			$memberCoachedQuery = $this->db->select('paid_coaching,level,CONCAT("https://","' . SERVER_NAME . '","/uploads/levels/",l.level_image) as level_image,CAST(coaching_minutes/60 as DECIMAL(11,1)) as coaching_minutes,l.level_description')
				->where('user_id', $post['user_id'])
				->join('levels as l', 'l.level_name=coach_details.level')
				->get('coach_details');

			$result = $memberCoachedQuery->result_array();

			if (!empty($result)) {
				$allResult['level'] = $result[0]['level'];
				$allResult['level_image'] = $result[0]['level_image'];
				$allResult['level_description'] = $result[0]['level_description'];
				$allResult['minutes_coached'] = $result[0]['coaching_minutes'];
			}

			return $allResult;
		}
    }