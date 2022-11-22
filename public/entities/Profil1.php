<?php

use App\Core\Entities\Profil;

class Profil1 extends Profil
{
    private $echelleSimple1;
    private $echelleSimple2;
    private $echelleSimple3;
    private $echelleSimple4;
    private $echelleSimple5;
    private $echelleComposite1;
    private $echelleComposite2;
    private $echelleComposite3;
    private $subtest1;
    private $subtest2;

    /**
     * @param $echelleSimple1
     * @param $echelleSimple2
     * @param $echelleSimple3
     * @param $echelleSimple4
     * @param $echelleSimple5
     * @param $echelleComposite1
     * @param $echelleComposite2
     * @param $echelleComposite3
     * @param $subtest1
     * @param $subtest2
     */
    public function __construct($echelleSimple1, $echelleSimple2, $echelleSimple3, $echelleSimple4, $echelleSimple5, $echelleComposite1, $echelleComposite2, $echelleComposite3, $subtest1, $subtest2)
    {
        $this->echelleSimple1 = $echelleSimple1;
        $this->echelleSimple2 = $echelleSimple2;
        $this->echelleSimple3 = $echelleSimple3;
        $this->echelleSimple4 = $echelleSimple4;
        $this->echelleSimple5 = $echelleSimple5;
        $this->echelleComposite1 = $echelleComposite1;
        $this->echelleComposite2 = $echelleComposite2;
        $this->echelleComposite3 = $echelleComposite3;
        $this->subtest1 = $subtest1;
        $this->subtest2 = $subtest2;
    }

    /**
     * @return mixed
     */
    public function getEchelleSimple1()
    {
        return $this->echelleSimple1;
    }

    /**
     * @return mixed
     */
    public function getEchelleSimple2()
    {
        return $this->echelleSimple2;
    }

    /**
     * @return mixed
     */
    public function getEchelleSimple3()
    {
        return $this->echelleSimple3;
    }

    /**
     * @return mixed
     */
    public function getEchelleSimple4()
    {
        return $this->echelleSimple4;
    }

    /**
     * @return mixed
     */
    public function getEchelleSimple5()
    {
        return $this->echelleSimple5;
    }

    /**
     * @return mixed
     */
    public function getEchelleComposite1()
    {
        return $this->echelleComposite1;
    }

    /**
     * @return mixed
     */
    public function getEchelleComposite2()
    {
        return $this->echelleComposite2;
    }

    /**
     * @return mixed
     */
    public function getEchelleComposite3()
    {
        return $this->echelleComposite3;
    }

    /**
     * @return mixed
     */
    public function getSubtest1()
    {
        return $this->subtest1;
    }

    /**
     * @return mixed
     */
    public function getSubtest2()
    {
        return $this->subtest2;
    }
}