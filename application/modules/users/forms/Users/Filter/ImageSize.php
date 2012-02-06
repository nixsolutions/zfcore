<?php
/**
 * Users_Form_Users_Filter_ImageSize
 *
 * @version $id$
 */
class Users_Form_Users_Filter_ImageSize implements Zend_Filter_Interface
{
    /**
     * @var integer
     */
    protected $_width;

    /**
     * @var integer
     */
    protected $_height;

    /**
     * @var bool
     */
    protected $_keepRatio = true;

    /**
     * Adds options to the filter at initiation
     *
     * @param string $options
     */
    public function __construct($options = null)
    {
        $width = null;
        $height = null;
        $keepRatio = null;

        if (!is_array($options)) {
            $width = $options;
            if (func_num_args() > 1) {
                $height = func_get_arg(1);
                if (func_num_args() > 2) {
                    $keepRatio = func_get_arg(2);
                }
            }
        } else {
            if (isset($options['width'])) {
                $width = $options['width'];
            }
            if (isset($options['height'])) {
                $height = $options['height'];
            }
            if (isset($options['keepRatio'])) {
                $keepRatio = $options['keepRatio'];
            }
        }

        if ($width) {
            $this->setWidth($width);
        }
        if ($height) {
            $this->setHeight($height);
        }
        if (is_bool($keepRatio)) {
            $this->setKeepRatio($keepRatio);
        }
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Lizard_Images_Filter_ImageSize
     */
    public function setWidth($width)
    {
        $this->_width = (int) $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Lizard_Images_Filter_ImageSize
     */
    public function setHeight($height)
    {
        $this->_height = (int) $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Set keep ratio
     *
     * @param boolen $keepRatio
     * @return Lizard_Images_Filter_ImageSize
     */
    public function setKeepRatio($keepRatio = true)
    {
        $this->_keepRatio = (bool) $keepRatio;

        return $this;
    }

    /**
     * Get keep ratio
     *
     * @return boolen
     */
    public function getKeepRatio()
    {
        return $this->_keepRatio;
    }

    /**
     * Resize file to width and height
     *
     * @param string $value
     */
    public function filter($value)
    {
        if (!file_exists($value)) {
            throw new Zend_Filter_Exception("File '$value' not found");
        }

        if (!is_writable($value)) {
            throw new Zend_Filter_Exception("File '$value' is not writable");
        }

        $toWidth = $this->getWidth();
        $toHeight = $this->getHeight();

        $image = imagecreatefromstring(file_get_contents($value));
        $width = imagesx($image);
        $height = imagesy($image);

        $startx = 0;
        $starty = 0;

        $resampled = imagecreatetruecolor($toWidth, $toHeight);

        if (!$toWidth && $toHeight) {
            $toWidth = $width / ($height / $toHeight);
        } elseif ($toWidth && !$toHeight) {
            $toHeight = $height / ($width / $toWidth);
        } elseif (!$toWidth && !$toHeight) {
            throw new Lizard_Images_Exception('Destination width and height not set');
        } elseif ($this->getKeepRatio()) {
            $xscale = $width / $toWidth;
            $yscale = $height / $toHeight;

            $startx = $toWidth;
            $starty = $toHeight;

            if ($yscale > $xscale) {
                $toWidth = $width / $yscale;
                $toHeight = $height / $yscale;
            } else {
                $toWidth = $width / $xscale;
                $toHeight = $height / $xscale;
            }
            $toWidth = round($toWidth);
            $toHeight = round($toHeight);

            $startx = ($startx - $toWidth) / 2;
            $starty = ($starty - $toHeight) / 2;
        }

        imagecopyresampled(
            $resampled,
            $image,
            $startx, $starty,
            0, 0,
            $toWidth,
            $toHeight,
            $width,
            $height
        );

        @unlink($value);

        $value = pathinfo($value, PATHINFO_DIRNAME) . '/'
               . pathinfo($value, PATHINFO_FILENAME) . '.jpg';
        imagejpeg($resampled, $value);

        return $value;
    }
}