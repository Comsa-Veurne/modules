<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Symfony\Component\Intl\Intl as Intl;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Add extends BackendBaseActionAdd
{
    public static function CreateMultipleCheckboxes($var)
    {
        $boxes = "<ul class='inputList'>";
        foreach ($var['items'] as $item) {
            $boxes .= "<li>";
            if (isset($item['title'])) {
                //$boxes .= $item['title'];
                $boxes .= '<input class="inputCheckbox" id="groups' . $item['id'] . '" type="checkbox" name="groups[]" value="' . $item['id'] . '" />';
                $boxes .= '<label for="groups' . $item['id'] . '">' . $item['title'] . '</label>';
            }
            if (isset($item['items'])) {

                $boxes .= self::CreateMultipleCheckboxes($item);
            }
            $boxes .= "</li>";
        }
        $boxes .= "</ul>";
        return $boxes;
    }

    private $fieldLanguages = array();

    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

        $this->frm = new BackendForm('add');
        $this->frm->addText('company');
        $this->frm->addText('name');
        $this->frm->addText('firstname');
        $this->frm->addText('email');
        $this->frm->addText('address');
        $this->frm->addText('zipcode');
        $this->frm->addText('city');
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), 'BE');

        $this->frm->addText('phone');
        $this->frm->addText('fax');

        $this->frm->addText('website');
        $this->frm->addEditor('text');
        $this->frm->addImage('image');
        $this->frm->addText('vat');
        $this->frm->addTextArea('zipcodes');
        $this->frm->addText('remark');
        //$this->frm->addText('assort');
        //$this->frm->addText('open');
        //$this->frm->addText('closed');
        //$this->frm->addText('visit');
        //$this->frm->addText('size');
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $fieldText = $this->frm->addEditor("text_" . strtolower($language));
            $fieldOpeningHours = $this->frm->addEditor("opening_hours_" . strtolower($language));

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["text"] = $fieldText->parse();
            $this->fieldLanguages[$key]["opening_hours"] = $fieldOpeningHours->parse();
        }

        //--Get all the users
        $groups = BackendAddressesModel::getAllGroups();
        if (!empty($groups)) {
            //--Loop all the group
            foreach ($groups as &$group) {
                $groupCheckboxes[] = array("label" => $group["title"], "value" => $group["id"]);
            }

            //--Add multicheckboxes to form
            $this->frm->addMultiCheckbox("groups", $groupCheckboxes);
        }

        $groups2 = BackendAddressesModel::getAllGroupsTreeArray();
        $this->tpl->assign('groups2', $groups2);

        $this->meta = new BackendMeta($this->frm, null, 'company', true);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // assign the url for the detail page
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }

        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            // validate the image
            if ($this->frm->getField('image')->isFilled()) {
                // image extension and mime type
                $this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
                $this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
            }

            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                $item['meta_id'] = $this->meta->save();
                $item['company'] = $fields['company']->getValue();
                $item['name'] = $fields['name']->getValue();
                $item['firstname'] = $fields['firstname']->getValue();
                $item['email'] = $fields['email']->getValue();
                $item['address'] = $fields['address']->getValue();
                $item['zipcode'] = $fields['zipcode']->getValue();
                $item['city'] = $fields['city']->getValue();
                $item['country'] = $fields['country']->getValue();
                $item['phone'] = $fields['phone']->getValue();
                $item['fax'] = $fields['fax']->getValue();
                $item['website'] = str_replace("http://", "", $fields['website']->getValue());
                $item['text'] = $fields['text']->getValue();
                $item['zipcodes'] = $fields['zipcodes']->getValue();
                $item['remark'] = $fields['remark']->getValue();
                //$item['assort'] = $fields['assort']->getValue();
                //$item['open'] = $fields['open']->getValue();
                //$item['closed'] = $fields['closed']->getValue();
                //$item['visit'] = $fields['visit']->getValue();
                //$item['size'] = $fields['size']->getValue();

                $item['language'] = BL::getWorkingLanguage();
                $item['hidden'] = $fields['hidden']->getValue();

                if ($item['country'] == '') {
                    $item['country'] = 'BE';
                }

                //--Create url
                $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($item['address'] . ', ' . $item['zipcode'] . ' ' . $item['city'] . ', ' . \SpoonLocale::getCountry($item['country'], BL::getWorkingLanguage())) . '&sensor=false';
                //--Get lat
                $geocode = json_decode(\SpoonHTTP::getContent($url));

                //--Sleep between the requests
                sleep(0.05);

                //--Check result
                $item['lat'] = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
                $item['lng'] = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/Addresses/Images';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/Source')) {
                    \SpoonDirectory::create($imagePath . '/Source');
                }
                if (!\SpoonDirectory::exists($imagePath . '/128x128')) {
                    \SpoonDirectory::create($imagePath . '/128x128');
                }
                if (!\SpoonDirectory::exists($imagePath . '/400x300')) {
                    \SpoonDirectory::create($imagePath . '/400x300');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x')) {
                    \SpoonDirectory::create($imagePath . '/800x');
                }

                // image provided?
                if ($this->frm->getField('image')->isFilled()) {
                    // build the image name
                    $item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

                    // upload the image & generate thumbnails
                    $this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
                }

                $item['id'] = BackendAddressesModel::insert($item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['text'] = $this->frm->getField('text_' . $language)->getValue();
                    $itemLanguage['opening_hours'] = $this->frm->getField('opening_hours_' . $language)->getValue();

                    BackendAddressesModel::insertLanguage($itemLanguage);
                }

                if (isset($fields["groups"])) {

                    //--Get all the groups
                    $groups = $fields["groups"]->getValue();
                    foreach ($groups as $value) {
                        $groupAddress = array();
                        $groupAddress["address_id"] = $item['id'];
                        $groupAddress["group_id"] = $value;

                        //--Add user to the group
                        BackendAddressesModel::insertAddressToGroup($groupAddress);
                    }
                }

                BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['name'], 'text' => $item['name']));

                BackendModel::triggerEvent($this->getModule(), 'after_add', $item);
                $this->redirect(BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']);
            }
        }
    }
}