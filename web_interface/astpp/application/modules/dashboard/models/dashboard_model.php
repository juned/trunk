<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
class Dashboard_model extends CI_Model {

    function Dashboard_model() 
    {
        parent::__construct();
    }

    function get_recent_recharge()
    {
	$accountinfo=$this->session->userdata('accountinfo');
	$userlevel_logintype=$this->session->userdata('userlevel_logintype');
	
	if($userlevel_logintype ==-1){
	  $where_arr=array('payment_by'=>-1);
	}
	if($userlevel_logintype == 1){
	  $where_arr=array('payment_by'=>$accountinfo['id']);
	}
	if($userlevel_logintype == 0 || $userlevel_logintype == 3){
	  $where_arr=array('accountid'=>$accountinfo['id']);
	}
        $this->db->where($where_arr);
        $this->db->select('id,accountid,credit,payment_date');
        $this->db->from('payments');
        $this->db->limit(10);
        $this->db->order_by('payment_date','desc');
	return $this->db->get();
    }
    function get_call_statistics() 
    {
	$start_date=date('Y-m-01');
	$end_date=date('Y-m-t');
	$accountinfo=$this->session->userdata('accountinfo');
    	$parent_id= ($accountinfo['type'] == -1) ? 0  :$accountinfo['id'];
    	$table=$accountinfo['type']==1? 'reseller_cdrs':'cdrs';
    	$profit_field_name=$table=='reseller_cdrs'?'reseller_cost' :'provider_call_cost';
    	$field_name=$table=='reseller_cdrs'?'accountid' :'reseller_id';
	$query = "select count(uniqueid) as sum,count(CASE WHEN disposition IN ('NORMAL_CLEARING','SUCCESS') THEN 1 END) as answered,count(CASE WHEN disposition NOT IN ('NORMAL_CLEARING','SUCCESS') THEN 1 END) as failed,sum(debit-".$profit_field_name.") as profit,DAY(callstart) as day from ".$table." where callstart >='". $start_date." 00:00:00' AND callstart <='". $end_date." 23:59:59' AND $field_name = '".$parent_id."' GROUP BY DAY(callstart)";
	return $this->db->query($query,false);
    }

    function get_customer_maximum_callminutes()
    {
	
	    $parent_id= ($this->session->userdata['accountinfo']['type'] == -1) ? 0  : $this->session->userdata['accountinfo']['id'];
	    $this->db->select('sum(billseconds) as billseconds,accountid');
	    $this->db->from('cdrs');
	    if($this->session->userdata('userlevel_logintype')!= 0 && $this->session->userdata('userlevel_logintype')!= 3)
	      $this->db->where('reseller_id',$parent_id);
	    else
	      $this->db->where('accountid',$parent_id);
	    $this->db->group_by('accountid');
	    $this->db->order_by('sum(billseconds)','desc');
	    $this->db->limit(10);
	    
	    return $this->db->get();
    }

    function get_customer_maximum_callcount()
    {
	    $parent_id= ($this->session->userdata['accountinfo']['type'] == -1) ? 0  : $this->session->userdata['accountinfo']['id'];
	    $this->db->select('count(uniqueid) as call_count,accountid');
	    $this->db->from('cdrs');
	    if($this->session->userdata('userlevel_logintype')!= 0 && $this->session->userdata('userlevel_logintype')!= 3)
	      $this->db->where('reseller_id',$parent_id);
	    else
	      $this->db->where('accountid',$parent_id);
	    $this->db->group_by('accountid');
	    $this->db->order_by('call_count','desc');
	    $this->db->limit(10);
	    return $this->db->get();
    }
}
?>
