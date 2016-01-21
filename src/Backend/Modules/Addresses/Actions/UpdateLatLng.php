<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

class UpdateLatLng extends BackendBaseActionIndex
{

    private $frm;

    private $records;

    private $response = "";
    private $responseError = "";

    public function execute()
    {
        parent::execute();

        $this->loadData();

        //		if($this->getParameter('load', 'string', null) == "ok")
        //		{
        //
        //		}

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    private function loadData()
    {
        $this->records = BackendAddressesModel::getAddressesWithoutLatLng();
    }

    /**
     * Load form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm("update");

        $this->frm->addDropdown('number_of_items', array_combine(range(10, 100, 10), range(10, 100, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_num_items', 10));
    }

    /**
     * Validate form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            if ($this->frm->isCorrect()) {
                if (!empty($this->records)) {

                    $teller = 0;

                    foreach ($this->records as $key => $record) {
                        if ($teller < $this->frm->getField("number_of_items")->getValue()) {

                            $data = array();

                            //--Create url
                            $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($record['address'] . ', ' . $record['zipcode'] . ' ' . $record['city'] . ', ' . \SpoonLocale::getCountry($record['country'], BL::getWorkingLanguage())) . '&sensor=false';
                            //--Get lat
                            $geocode = json_decode(\SpoonHTTP::getContent($url));

                            //--Sleep between the requests
                            sleep(0.05);

                            //--Check result
                            $data['lat'] = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
                            $data['lng'] = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;

                            if ($data['lat'] != null) {
                                BackendAddressesModel::update($record['id'], $data);

                                $this->response .= "<strong>" . $record['company'] . "</strong> - " . $record['address'] . " " . $record['zipcode'] . " " . $record['city'] . " <i>(Lat: " . $data['lat'] . ", Lng: " . $data['lng'] . ")</i><br/>";

                                //--Delete from array
                                unset($this->records[$key]);
                            } else {

                                $data['lat'] = "notfound";
                                $data['lng'] = "notfound";
                                BackendAddressesModel::update($record['id'], $data);

                                $this->responseError .= "<strong>" . $record['company'] . "</strong> - " . $record['address'] . " " . $record['zipcode'] . " " . $record['city'] . "<br/>";
                            }
                        } else {
                            break;
                        }

                        //--Add teller
                        $teller++;
                    }
                    $this->tpl->assign("responseError", $this->responseError);
                    $this->tpl->assign("response", $this->response);
                }
            }
        }
    }

    public function parse()
    {
        parent::parse();

        $this->tpl->assign("number_of_addressess", count($this->records));
        $this->frm->parse($this->tpl);
    }
}