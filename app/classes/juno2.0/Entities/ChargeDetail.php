<?php

namespace ODJuno\Entities;

use ODJuno\Exception\ODJunoException;

class ChargeDetail extends BaseEntity
{
    /**
     * @var string $pixKey
     */
    protected $pixKey;
    /**
     * @var string $description
     */
    protected $description;
    /**
     * @var array $references
     */
    protected $references;
    /**
     * @var float $totalAmount
     */
    protected $totalAmount;
    /**
     * @var float $amount
     */
    protected $amount;
    /**
     * @var Date $dueDate
     */
    protected $dueDate;
    /**
     * @var int $installments
     */
    protected $installments;
    /**
     * @var int $maxOverdueDays
     */
    protected $maxOverdueDays;
    /**
     * @var float $fine
     */
    protected $fine;
    /**
     * @var float $interest
     */
    protected $interest;
    /**
     * @var float $discountAmount
     */
    protected $discountAmount;
    /**
     * @var int $discountDays
     */
    protected $discountDays;
    /**
     * @var array $paymentTypes
     */
    protected $paymentTypes;
    /**
     * @var bool $paymentAdvance
     */
    protected $paymentAdvance;
    /**
     * @var string $feeSchemaToken
     */
    protected $feeSchemaToken;
    /**
     * @var array of Split $split
     */
    protected $split;

    const PAYMENT_TYPE_BOLETO      = 'BOLETO';
    const PAYMENT_TYPE_BOLETO_PIX  = 'BOLETO_PIX';
    const PAYMENT_TYPE_CREDIT_CARD = 'CREDIT_CARD';

    /**
     * Get $pixKey
     *
     * @return  string
     */ 
    public function getPixKey()
    {
        return $this->pixKey;
    }

    /**
     * Get $description
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get $references
     *
     * @return  array
     */ 
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Get $totalAmount
     *
     * @return  float
     */ 
    public function getTotalAmount()
    {
        return $this->totalAmount;
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
     * Get $dueDate
     *
     * @return  Date
     */ 
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Get $installments
     *
     * @return  int
     */ 
    public function getInstallments()
    {
        return $this->installments;
    }

    /**
     * Get $maxOverdueDays
     *
     * @return  int
     */ 
    public function getMaxOverdueDays()
    {
        return $this->maxOverdueDays;
    }

    /**
     * Get $fine
     *
     * @return  float
     */ 
    public function getFine()
    {
        return $this->fine;
    }

    /**
     * Get $interest
     *
     * @return  float
     */ 
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * Get $discountAmount
     *
     * @return  float
     */ 
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Get $discountDays
     *
     * @return  int
     */ 
    public function getDiscountDays()
    {
        return $this->discountDays;
    }

    /**
     * Get $paymentTypes
     *
     * @return  array
     */ 
    public function getPaymentTypes()
    {
        return $this->paymentTypes;
    }

    /**
     * Get $paymentAdvance
     *
     * @return  bool
     */ 
    public function getPaymentAdvance()
    {
        return $this->paymentAdvance;
    }

    /**
     * Get $feeSchemaToken
     *
     * @return  string
     */ 
    public function getFeeSchemaToken()
    {
        return $this->feeSchemaToken;
    }

    /**
     * Get of Split $split
     *
     * @return  array
     */ 
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * Set $pixKey
     *
     * @param  string  $pixKey  $pixKey
     *
     * @return  self
     */ 
    public function setPixKey(string $pixKey)
    {
        $this->pixKey = $pixKey;

        return $this;
    }

    /**
     * Set $description
     *
     * @param  string  $description  $description
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set $references
     *
     * @param  array  $references  $references
     *
     * @return  self
     */ 
    public function setReferences(array $references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * Set $totalAmount
     *
     * @param  float  $totalAmount  $totalAmount
     *
     * @return  self
     */ 
    public function setTotalAmount(float $totalAmount)
    {
        $this->totalAmount = $totalAmount;

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
     * Set $dueDate
     *
     * @param  Date  $dueDate  $dueDate
     *
     * @return  self
     */ 
    public function setDueDate(Date $dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Set $installments
     *
     * @param  int  $installments  $installments
     *
     * @return  self
     */ 
    public function setInstallments(int $installments)
    {
        $this->installments = $installments;

        return $this;
    }

    /**
     * Set $maxOverdueDays
     *
     * @param  int  $maxOverdueDays  $maxOverdueDays
     *
     * @return  self
     */ 
    public function setMaxOverdueDays(int $maxOverdueDays)
    {
        $this->maxOverdueDays = $maxOverdueDays;

        return $this;
    }

    /**
     * Set $fine
     *
     * @param  float  $fine  $fine
     *
     * @return  self
     */ 
    public function setFine(float $fine)
    {
        $this->fine = $fine;

        return $this;
    }

    /**
     * Set $interest
     *
     * @param  float  $interest  $interest
     *
     * @return  self
     */ 
    public function setInterest(float $interest)
    {
        $this->interest = $interest;

        return $this;
    }

    /**
     * Set $discountAmount
     *
     * @param  float  $discountAmount  $discountAmount
     *
     * @return  self
     */ 
    public function setDiscountAmount(float $discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Set $discountDays
     *
     * @param  int  $discountDays  $discountDays
     *
     * @return  self
     */ 
    public function setDiscountDays(int $discountDays)
    {
        $this->discountDays = $discountDays;

        return $this;
    }

    /**
     * Set $paymentTypes
     *
     * @param  array  $paymentTypes  $paymentTypes
     *
     * @return  self
     */ 
    public function setPaymentTypes(array $paymentTypes)
    {
        if (in_array(self::PAYMENT_TYPE_BOLETO, $paymentTypes)
            && in_array(self::PAYMENT_TYPE_BOLETO_PIX, $paymentTypes)) {    
                throw new ODJunoException('Payment type arrangements NOT allowed: BOLETO, BOLETO_PIX');
        }

        $this->paymentTypes = $paymentTypes;

        return $this;
    }

    /**
     * Set $paymentAdvance
     *
     * @param  bool  $paymentAdvance  $paymentAdvance
     *
     * @return  self
     */ 
    public function setPaymentAdvance(bool $paymentAdvance)
    {
        $this->paymentAdvance = $paymentAdvance;

        return $this;
    }

    /**
     * Set $feeSchemaToken
     *
     * @param  string  $feeSchemaToken  $feeSchemaToken
     *
     * @return  self
     */ 
    public function setFeeSchemaToken(string $feeSchemaToken)
    {
        $this->feeSchemaToken = $feeSchemaToken;

        return $this;
    }

    /**
     * Set of Split $split
     *
     * @param  array  $split  of Split $split
     *
     * @return  self
     */ 
    public function setSplit(array $split)
    {
        $this->split = $split;

        return $this;
    }

    /**
     * Add a Split $split
     *
     * @param  array  $split  of Split $split
     *
     * @return  self
     */ 
    public function addSplit(Split $split)
    {
        $this->split[] = $split;

        return $this;
    }
}