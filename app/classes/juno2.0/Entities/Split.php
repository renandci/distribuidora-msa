<?php

namespace ODJuno\Entities;

class Split extends BaseEntity
{
    /**
     * @var string $recipientToken
     */
    protected $recipientToken;
    /**
     * @var float $amount
     */
    protected $amount;
    /**
     * @var float $percentage
     */
    protected $percentage;
    /**
     * @var bool $amountRemainder
     */
    protected $amountRemainder;
    /**
     * @var bool $chargeFee
     */
    protected $chargeFee;

    /**
     * Get $recipientToken
     *
     * @return  string
     */ 
    public function getRecipientToken()
    {
        return $this->recipientToken;
    }

    /**
     * Get $amount
     *
     * @return  float
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get $percentage
     *
     * @return  float
     */ 
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Get $amountRemainder
     *
     * @return  bool
     */ 
    public function getAmountRemainder()
    {
        return $this->amountRemainder;
    }

    /**
     * Get $chargeFee
     *
     * @return  bool
     */ 
    public function getChargeFee()
    {
        return $this->chargeFee;
    }



    ///// Seters



    /**
     * Set $recipientToken
     *
     * @param  string  $recipientToken  $recipientToken
     *
     * @return  self
     */ 
    public function setRecipientToken(string $recipientToken)
    {
        $this->recipientToken = $recipientToken;

        return $this;
    }

    /**
     * Set $amount
     *
     * @param  float  $amount  $amount
     *
     * @return  self
     */ 
    public function setAmount(float $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set $percentage
     *
     * @param  float  $percentage  $percentage
     *
     * @return  self
     */ 
    public function setPercentage(float $percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Set $amountRemainder
     *
     * @param  bool  $amountRemainder  $amountRemainder
     *
     * @return  self
     */ 
    public function setAmountRemainder(bool $amountRemainder)
    {
        $this->amountRemainder = $amountRemainder;

        return $this;
    }

    /**
     * Set $chargeFee
     *
     * @param  bool  $chargeFee  $chargeFee
     *
     * @return  self
     */ 
    public function setChargeFee(bool $chargeFee)
    {
        $this->chargeFee = $chargeFee;

        return $this;
    }
}