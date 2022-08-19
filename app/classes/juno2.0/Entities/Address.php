<?php

namespace ODJuno\Entities;

class Address extends BaseEntity
{
    /**
     * @var string $street
     */
    protected $street;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $complement;

    /**
     * @var string
     */
    protected $neighborhood;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $postCode;


        /**
     * Get the value of street
     *
     * @return  string
     */ 
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Get the value of number
     *
     * @return  string
     */ 
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get the value of complement
     *
     * @return  string
     */ 
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * Get the value of neighborhood
     *
     * @return  string
     */ 
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * Get the value of city
     *
     * @return  string
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the value of state
     *
     * @return  string
     */ 
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the value of postCode
     *
     * @return  string
     */ 
    public function getPostCode()
    {
        return $this->postCode;
    }



    

    /**
     * Set $street
     *
     * @param  string  $street  $street
     *
     * @return  self
     */ 
    public function setStreet(string $street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Set the value of number
     *
     * @param  string  $number
     *
     * @return  self
     */ 
    public function setNumber(string $number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Set the value of complement
     *
     * @param  string  $complement
     *
     * @return  self
     */ 
    public function setComplement(string $complement)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Set the value of neighborhood
     *
     * @param  string  $neighborhood
     *
     * @return  self
     */ 
    public function setNeighborhood(string $neighborhood)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * Set the value of city
     *
     * @param  string  $city
     *
     * @return  self
     */ 
    public function setCity(string $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set the value of state
     *
     * @param  string  $state
     *
     * @return  self
     */ 
    public function setState(string $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Set the value of postCode
     *
     * @param  string  $postCode
     *
     * @return  self
     */ 
    public function setPostCode(string $postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }
}
