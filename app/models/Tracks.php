<?php

namespace App\Models\Tracks;

class Tracks extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $service;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $download_link;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field service
     *
     * @param string $service
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field download_link
     *
     * @param string $download_link
     * @return $this
     */
    public function setDownloadLink($download_link)
    {
        $this->download_link = $download_link;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field download_link
     *
     * @return string
     */
    public function getDownloadLink()
    {
        return $this->download_link;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("parser");
        $this->setSource("tracks");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tracks';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tracks[]|Tracks|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tracks|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
