<?php

namespace ODJuno\Entities;

use ODJuno\Exception\ODJunoException;

class Charge extends BaseEntity
{
    /**
     * @var ChargeDetail $charge
     */
    protected $charge;
    /**
     * @var Billing $billing
     */
    protected $billing;
    

    /**
     * Get $chargeDetail
     *
     * @return  ChargeDeail
     */ 
    public function getChargeDetail()
    {
        $this->charge = empty($this->charge) ? new ChargeDetail() : $this->charge;
        return $this->charge;
    }

    /**
     * Get $billing
     *
     * @return  Billing
     */ 
    public function getBilling()
    {
        $this->billing = empty($this->billing) ? new Billing() : $this->billing;
        return $this->billing;
    }



    

    /**
     * Set $chargeDetail
     *
     * @param  ChargeDeail  $chargeDetail  $chargeDetail
     *
     * @return  self
     */ 
    public function setChargeDetail(ChargeDeail $chargeDetail)
    {
        $this->chargeDetail = $chargeDetail;

        return $this;
    }

    /**
     * Set $billing
     *
     * @param  Billing  $billing  $billing
     *
     * @return  self
     */ 
    public function setBilling(Billing $billing)
    {
        $this->billing = $billing;

        return $this;
    }
}