<?php

namespace ODJuno\Entities;

class Billing extends BaseEntity
{
    /**
     * @var string $name
     */
    protected $name;
    /**
     * @var string $document
     */
    protected $document;
    /**
     * @var string $email
     */
    protected $email;
    /**
     * @var Address $address
     */
    protected $address;
    /**
     * @var string $secondaryEmail
     */
    protected $secondaryEmail;
    /**
     * @var string $phone
     */
    protected $phone;
    /**
     * @var Date $birthDate
     */
    protected $birthDate;
    /**
     * @var bool $notify
     */
    protected $notify;

    /**
     * Get $name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get $document
     *
     * @return  string
     */ 
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Get $email
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get $address
     *
     * @return  Address
     */ 
    public function getAddress()
    {
        return empty($this->address) ? new Address() : $this->address;
    }

    /**
     * Get $secondaryEmail
     *
     * @return  string
     */ 
    public function getSecondaryEmail()
    {
        return $this->secondaryEmail;
    }

    /**
     * Get $phone
     *
     * @return  string
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get $birthDate
     *
     * @return  Date
     */ 
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Get $notify
     *
     * @return  bool
     */ 
    public function getNotify()
    {
        return $this->notify;
    }



    

    /**
     * Set $name
     *
     * @param  string  $name  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set $document
     *
     * @param  string  $document  $document
     *
     * @return  self
     */ 
    public function setDocument(string $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Set $email
     *
     * @param  string  $email  $email
     *
     * @return  self
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set $address
     *
     * @param  Address  $address  $address
     *
     * @return  self
     */ 
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Set $secondaryEmail
     *
     * @param  string  $secondaryEmail  $secondaryEmail
     *
     * @return  self
     */ 
    public function setSecondaryEmail(string $secondaryEmail)
    {
        $this->secondaryEmail = $secondaryEmail;

        return $this;
    }

    /**
     * Set $phone
     *
     * @param  string  $phone  $phone
     *
     * @return  self
     */ 
    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Set $birthDate
     *
     * @param  Date  $birthDate  $birthDate
     *
     * @return  self
     */ 
    public function setBirthDate(Date $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Set $notify
     *
     * @param  bool  $notify  $notify
     *
     * @return  self
     */ 
    public function setNotify(bool $notify)
    {
        $this->notify = $notify;

        return $this;
    }
}