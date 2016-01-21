<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Symfony\Component\Intl\Intl;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Edit extends BackendBaseActionEdit
{
    public static function CreateMultipleCheckboxes($var, $var2)
    {
        $boxes = "<ul class='inputList'>";
        foreach ($var['items'] as $item) {
            $boxes .= "<li>";
            if (isset($item['title'])) {
                //$boxes .= $item['title'];
                $boxes .= '<input class="inputCheckbox" id="groups' . $item['id'] . '" type="checkbox" name="groups[]" value="' . $item['id'] . '" ' . (in_array($item['id'], $var2) ? 'checked="checked"' : '') . ' />';
                $boxes .= '<label for="groups' . $item['id'] . '">' . $item['title'] . '</label>';
            }
            if (isset($item['items'])) {

                $boxes .= self::CreateMultipleCheckboxes($item, $var2);
            }
            $boxes .= "</li>";
        }
        $boxes .= "</ul>";
        return $boxes;
    }

    private $fieldLanguages = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);
        if ($this->id == null || !BackendAddressesModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }

        $this->record = BackendAddressesModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

        $this->frm = new BackendForm('edit');
        $this->frm->addText('company', $this->record['company']);
        $this->frm->addText('name', $this->record['name']);
        $this->frm->addText('firstname', $this->record['firstname']);
        $this->frm->addText('email', $this->record['email']);
        $this->frm->addText('address', $this->record['address']);
        $this->frm->addText('zipcode', $this->record['zipcode']);
        $this->frm->addText('city', $this->record['city']);
//        $this->frm->addText('country', $this->record['country']);
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), $this->record['country']);
        $this->frm->addText('phone', $this->record['phone']);
        $this->frm->addText('fax', $this->record['fax']);
        $this->frm->addText('website', $this->record['website']);
        $this->frm->addText('vat', $this->record['vat']);
        $this->frm->addTextArea('zipcodes', $this->record['zipcodes']);
        $this->frm->addText('remark', $this->record['remark']);
        //$this->frm->addText('assort', $this->record['assort']);
        //$this->frm->addText('open', $this->record['open']);
        //$this->frm->addText('closed', $this->record['closed']);
        //$this->frm->addText('visit', $this->record['visit']);
        //$this->frm->addText('size', $this->record['size']);
        //$this->frm->addEditor('text', $this->record['text']);
        $this->frm->addImage('image');
        $this->frm->addCheckbox('delete_image');

        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $addressesLanguage = BackendAddressesModel::getLanguage($this->id, $language);

            $fieldText = $this->frm->addEditor("text_" . strtolower($language), $addressesLanguage['text']);
            $fieldOpeningHours = $this->frm->addEditor("opening_hours_" . strtolower($language), $addressesLanguage['opening_hours']);

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["text"] = $fieldText->parse();
            $this->fieldLanguages[$key]["opening_hours"] = $fieldOpeningHours->parse();
        }

        //--Get all the groups
        $groups = BackendAddressesModel::getAllGroups();

        if (!empty($groups)) {
            //--Loop all the users
            foreach ($groups as &$group) {
                $groupCheckboxes[] = array("label" => $group["title"], "value" => $group["id"]);
            }

            //--Get the users from the group
            $groupsAddress = BackendAddressesModel::getGroupsForAddress($this->id);

            //--Create a selected-array
            $groupCheckboxesSelected = count($groupsAddress) > 0 ? array_keys($groupsAddress) : null;

            //--Add multicheckboxes to form
            $this->frm->addMultiCheckbox("groups", $groupCheckboxes, $groupCheckboxesSelected);
        }

        $groups2 = BackendAddressesModel::getAllGroupsTreeArray();
        $this->tpl->assign('groups2', $groups2);
        $this->tpl->assign('groups2selected', $groupCheckboxesSelected == null ? array() : $groupCheckboxesSelected);
        // meta
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'company', true);
        $this->meta->setUrlCallback('Backend\Modules\Addresses\Engine\Model', 'getUrl', array($this->record['id']));
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();
        $this->tpl->assign('item', $this->record);

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
        $this->tpl->assign("fieldLanguages", $this->fieldLanguages);
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
            //			$fields['name']->isFilled(BL::err('FieldIsRequired'));

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
                $item['zipcodes'] = $fields['zipcodes']->getValue();
                $item['remark'] = $fields['remark']->getValue();
                //$item['text'] = $fields['text']->getValue();
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

                $item['image'] = $this->record['image'];

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

                // if the image should be deleted
                if ($this->frm->getField('delete_image')->isChecked()) {
                    // delete the image
                    \SpoonFile::delete($imagePath . '/Source/' . $item['image']);

                    // reset the name
                    $item['image'] = null;
                }

                // new image given?
                if ($this->frm->getField('image')->isFilled()) {
                    // delete the old image
                    \SpoonFile::delete($imagePath . '/Source/' . $this->record['image']);

                    // build the image name
                    $item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

                    // upload the image & generate thumbnails
                    $this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
                } // rename the old image
                elseif ($item['image'] != null) {
                    // get the old file extension
                    $imageExtension = \SpoonFile::getExtension($imagePath . '/Source/' . $item['image']);

                    // get the new image name
                    $newName = $this->meta->getURL() . '.' . $imageExtension;

                    // only change the name if there is a difference
                    if ($newName != $item['image']) {
                        // loop folders
                        foreach (BackendModel::getThumbnailFolders($imagePath, true) as $folder) {
                            // move the old file to the new name
                            \SpoonFile::move($folder['path'] . '/' . $item['image'], $folder['path'] . '/' . $newName);
                        }

                        // assign the new name to the database
                        $item['image'] = $newName;
                    }
                }

                BackendAddressesModel::update($this->id, $item);
                $item['id'] = $this->id;

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['text'] = $this->frm->getField('text_' . $language)->getValue();
                    $itemLanguage['opening_hours'] = $this->frm->getField('opening_hours_' . $language)->getValue();

                    BackendAddressesModel::updateLanguage($itemLanguage);
                }

                if (isset($fields["groups"])) {

                    //--Get all the groups
                    $groups = $fields["groups"]->getValue();
                    BackendAddressesModel::deleteGroupsFromAddress($item['id']);
                    foreach ($groups as $value) {
                        $groupAddress = array();
                        $groupAddress["address_id"] = $item['id'];
                        $groupAddress["group_id"] = $value;

                        //--Add user to the group
                        BackendAddressesModel::insertAddressToGroup($groupAddress);
                    }
                }

                BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['name'], 'text' => $item['name']));

                BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
                $this->redirect(BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']);
            }
        }
    }
}