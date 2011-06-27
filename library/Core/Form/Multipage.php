<?php
/**
 * Multipage form
 *
 * @category   Library
 * @package    Core_Form
 *
 * @author     Dmitriy Britan <dmitriy.britan@nixsolutions.com>
 */
class Core_Form_Multipage extends Zend_Form
{
    /**
     * Session namespace
     *
     * @var string
     */
    protected $_namespace = '';

    /**
     * Session access variable
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Current subform to display (set by user)
     *
     * @var string
     */
    protected $_current;

    /**
     * Get the session namespace we're using
     *
     * @return Zend_Session_Namespace
     */
    public function getSessionNamespace()
    {
        if (empty($this->_namespace)) {
            throw new Core_Exception('Session namespace for multipage form undefined');
        }

        if (null === $this->_session) {
            $this->_session = new Zend_Session_Namespace($this->getNamespace());
        }

        return $this->_session;
    }

    /**
     * Reset values of form
     *
     * @return Zend_Form
     */
    public function reset()
    {
        $this->getSessionNamespace()->unsetAll();
        return parent::reset();
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        if (sizeof($data) == 1) {
            $subForm = $this->getSubForm(key($data));
        } else {
            return false;
        }

        if ($subForm) {
            if ($subForm->isValid($data)) {
                $values = $subForm->getValues();
                $key = key($values);
                $this->getSessionNamespace()->$key = $values[$key];
            } else {
                $this->setCurrent($subForm->getName());
            }
        } else {
            return false;
        }

        $this->_sort();
        end($this->_order);

        // Is last subform?
        if (key($data) == key($this->_order)) {
            $totalData = array();
            foreach ($this->getSessionNamespace() as $key => $info) {
                $totalData[$key] = $info;
            }

            return parent::isValid($totalData);
        }

        return false;
    }

    /**
     * Method set current subform
     *
     * @param string $value
     * @return Core_Form_Multipage
     */
    public function setCurrent($value)
    {
        $this->_current = $value;
        return $this;
    }

    /**
     * Return current subform
     *
     * @param  int $step
     * @return null|Zend_Form_SubForm
     */
    public function getCurrent()
    {
        if (sizeof($this->getSubForms()) < 1) {
            throw new Core_Exception('Multipage form don\'t have any subforms');
        }
        
        $this->_sort();

        $step = $this->_current;
        
        if ((null !== $step)
            && (!empty($this->getSessionNamespace()->$step))) {
            $subForm = $this->getSubForm($step);
            $subForm->populate($this->getSessionNamespace()->$step);
        } else {
            foreach ($this->_order as $key => $order) {
                if (!$this->isStored($key)) {
                    $subForm = $this->getSubForm($key);
                    break;
                }
            }
        }
        
        //FIXME: Undefined variable: subForm
        if (isset($subForm)) {
            if (is_null($subForm)) {
                end($this->_order);
                $subForm = $this->getSubForm(key($this->_order));
            }
            return $this->_prepareSubForm($subForm);
        }
        
        return false;
    }

    /**
     * Serialize as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getCurrent()->__toString();
    }

    /**
     * Prepare a subform for display, can be redeclared in child classes
     *
     * @param  string|Zend_Form_SubForm $subForm
     * @return Zend_Form_SubForm
     */
    protected function _prepareSubForm(Zend_Form_SubForm $subForm)
    {
        return $subForm;
    }

    /**
     * Namespace setter
     *
     * @param string $value
     * @return Core_Form_Multipage
     */
    public function setNamespace($value)
    {
        $this->_namespace = $value;
        return $this;
    }

    /**
     * Namaspace getter
     *
     * @return Zend_Session_Namespace
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Retrieve all form subForms/subforms
     *
     * @param bool $sort
     * @return array
     */
    public function getSubForms($sort = false)
    {
        if (!$sort) {
            return parent::getSubForms();
        } else {
            $this->_sort();

            $subForms = array();
            foreach ($this->_order as $key => $order) {
                $subForms[] = $this->getSubForm($key);
            }

            return $subForms;
        }
    }

    /**
     * Method check subform data is storage
     *
     * @param Zend_Form_SubForm|string $subForm
     * @return bool
     */
    public function isStored($subForm)
    {
        if ($subForm instanceof Zend_Form_SubForm) {
            $key = $subForm->getName();
        } else {
            $key = $subForm;
        }

        return ($this->getSessionNamespace()->$key) ? true : false;
    }
}
