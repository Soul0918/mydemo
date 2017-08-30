<?php

namespace Common\Jobs;

class BillUpdateJob
{
    public function perform()
    {
        try {
            \Think\Log::write(__CLASS__ . '\\' . __FUNCTION__ . ':' . '开始运行:---------------------------------------------------');
            $type = ( int )$this->args ['type'];
            $id = ( int )$this->args ['id'];
            switch ($type) {
                case 2 :
                    $this->bill_detail_summary_by_bill_id($id);
                    break;
                default :
                    $this->bill_summary($id);
                    break;
            }
            \Think\Log::write('结束运行:---------------------------------------------------');
        } catch (Exception $e) {
            D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $e->getMessage());
        }
    }

    /**
     * 统计数据
     *
     * @author Link
     * @param mixed $bill_main_id
     */
    private function bill_summary($bill_main_id)
    {
        $bill_main_id = ( int )$bill_main_id;
        if ($bill_main_id > 0) {
            $map ['bill_main_id'] = $bill_main_id;
            $map ['state'] = 2;
            $data_main = D('BillMain')->where($map)->find();
            if (!empty ($data_main)) {
                // 更新Summary Main>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                $model_summary = D('BillSummary');
                $map = array(
                    "company_id" => $data_main ['company_id'],
                    "community_id" => $data_main ['community_id'],
                    "bill_date" => $data_main ['bill_date']
                );
                $data_summary = $model_summary->getData($map)->find();
                if (empty ($data_summary)) {
                    $data_summary = [];
                    $data_summary ['company_id'] = $data_main ['company_id'];
                    $data_summary ['community_id'] = $data_main ['community_id'];
                    $data_summary ['bill_date'] = $data_main ['bill_date'];
                    $data_summary ['summary_id'] = 0;
                    $data_summary ['receivable'] = 0;
                    $data_summary ['received'] = 0;
                    $data_summary ['received_last'] = 0;
                    $data_summary ['owe'] = 0;
                    $data_summary ['owe_total'] = 0;
                }
                // 获取应收金额
                $data_bills = D('Bills')->getData($map, false)->field('b.bill_date, a.company_id, a.community_id, ifnull(sum(a.amount), 0) as amount')->find();
                if (!empty ($data_bills)) {
                    $data_summary ['receivable'] = $data_bills ['amount'];
                }
                $map = array(
                    "company_id" => $data_main ['company_id'],
                    "community_id" => $data_main ['community_id'],
                    "payment_year" => date('Y', strtotime($data_main ['bill_date'])),
                    "payment_month" => date('m', strtotime($data_main ['bill_date']))
                );
                // 获取实收金额
                $data_bills = D('Bills')->getData($map, false)->field('b.bill_date, a.company_id, a.community_id, ifnull(sum(a.amount), 0) as received, ifnull(sum(case when year(a.payment_date) <> year(b.bill_date) or month(a.payment_date) <> month(b.bill_date) then a.amount else 0 end), 0) as received_last')->find();
                if (!empty ($data_bills)) {
                    $data_summary ['received'] = $data_bills ['received'];
                    $data_summary ['received_last'] = $data_bills ['received_last'];
                }
                $data_summary ['owe'] = $data_summary ['receivable'] - $data_summary ['received'] + $data_summary ['received_last'];
                if ($data_summary ['owe'] < 0) {
                    $data_summary ['owe'] = 0;
                }

                $this->_update_summary_main($data_summary);
                // 结束>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

                //更新detail
                $this->bill_detail_summary_by_main($bill_main_id);
            }
        }
    }

    /**
     *
     * @param $bill_main_id
     */
    private function bill_detail_summary_by_main($bill_main_id)
    {
        $bill_main_id = ( int )$bill_main_id;
        if ($bill_main_id > 0) {
            $map = ['main_state' => 2, 'bill_main_id' => $bill_main_id];
            $map['room_id'] = ['egt', 1];
            $data_bill = D('Bills')->getData($map)->select();
            if (count($data_bill) > 0) {
                foreach ($data_bill as $value) {
                    //$this->bill_detail_summary_by_bill_id($value['bill_id']);
                    $this->bill_detail_summary_by_room_id($value['room_id'], $value['bill_date']);
                }
            }
        }
    }

    /**
     * 根据账单id更新统计
     * @param $bill_id
     */
    private function bill_detail_summary_by_bill_id($bill_id)
    {
        $bill_id = (int)$bill_id;
        if ($bill_id > 0) {
            $model_bill = D('Bills');
            $map = ['main_state' => 2, 'bill_id' => $bill_id];
            $data_bill = $model_bill->getData($map)->find();
            if ($data_bill != null && $data_bill['room_id'] > 0) {
                $this->bill_detail_summary_by_room_id($data_bill['room_id'], $data_bill['bill_date']);
            }
        }
    }

    /**
     * 根据房间id更新统计
     * @param $room_id
     * @param $date
     */
    private function bill_detail_summary_by_room_id($room_id, $date)
    {
        $room_id = (int)$room_id;
        if ($room_id > 0) {
            $map = ['main_state' => 2, 'bill_date' => $date . '-01', 'room_id' => $room_id];
            $fields = 'a.company_id, a.community_id, a.unit_id, a.room_id, b.bill_date, ';
            $fields = $fields . 'sum(a.amount) as receivable, ';
            $fields = $fields . 'sum(case when a.state in (2,3,4) then a.amount else 0 end) as received, ';
            $fields = $fields . 'count(1) as periods, ';
            $fields = $fields . 'sum(case a.state when 1 then 1 else 0 end) as owe_period ';
            $model_bill = D('Bills');
            $data_summary = $model_bill->getData($map)->field($fields)->find();
            $data_summary['owe'] = $data_summary['receivable'] - $data_summary['received'];
            $data_summary['bill_date'] = $map['bill_date'];

            $this->_update_summary_detail($data_summary);
        }
    }

    /**
     * 更新数据
     *
     * @param mixed $data_summary
     */
    private function _update_summary_main($data_summary)
    {
        $model_summary = D('BillSummary');
        $data_summary = $model_summary->create($data_summary);
        if ($data_summary) {
            if ($data_summary ['summary_id'] > 0) {
                if ($model_summary->save($data_summary) == false && $model_summary->getError() <> '') {
                    D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
                }
            } else {
                if (!$model_summary->add($data_summary)) {
                    D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
                }
            }
        } else {
            D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
        }
    }

    /**
     * 更新物业账单汇总明细
     * @param $data_summary
     */
    private function _update_summary_detail($data_summary)
    {
        $model_summary = D('BillSummaryDetail');
        $map = ['bill_date' => $data_summary['bill_date'], 'room_id' => $data_summary['room_id']];
        $data = $model_summary->getData($map)->find();
        if ($data != null) {
            $data_summary['summary_id'] = $data['summary_id'];
        } else {
            $data_summary['summary_id'] = 0;
        }
        $data_summary = $model_summary->create($data_summary);
        if ($data_summary) {
            if ($data_summary ['summary_id'] > 0) {
                if ($model_summary->save($data_summary) == false && $model_summary->getError() <> '') {
                    D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
                }
            } else {
                if (!$model_summary->add($data_summary)) {
                    D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
                }
            }
        } else {
            D('Log')->add_log(__CLASS__ . '\\' . __FUNCTION__, $model_summary->getError() . $model_summary->getDbError());
        }
    }
}