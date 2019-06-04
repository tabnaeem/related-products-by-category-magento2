<?php

namespace Tabish\RelatedProductByCategories\Helper;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
	/** \Magento\Catalog\Helper\Image */
	protected $_imageHelper;
	protected $_listing_default_image = "category_page_grid";

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Helper\Image $imageHelper
		){
		$this->_imageHelper = $imageHelper;
		parent::__construct($context);
	}

	public function setListingDefaultImage($image_version = ""){
		if($image_version){
			$this->_listing_default_image = $image_version;
		}
		return $this;
	}

	public function getListingDefaultImage(){
		return $this->_listing_default_image;
	}

	/**
	 * Get image URL of the given product
	 *
	 * @param \Magento\Catalog\Model\Product	$product		Product
	 * @param int							    $w				Image width
	 * @param int							    $h				Image height
	 * @param string						    $imgVersion		Image version: image, small_image, thumbnail
	 * @param mixed							    $file			Specific file
	 * @return string
	 */
	public function getImg($product, $w=300, $h, $imgVersion='image', $file=NULL)
	{
		if ($h <= 0){
			$image = $this->_imageHelper
			->init($product, $imgVersion)
			->constrainOnly(true)
			->keepAspectRatio(true)
			->keepFrame(false);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w);
			return $image;
		}else{
			$image = $this->_imageHelper
			->init($product, $imgVersion);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w, $h);
			return $image;
		}
	}

	/**
     * Get alternative image HTML of the given product
     *
     * @param \Magento\Catalog\Model\Product    $product        Product
     * @param int                               $w              Image width
     * @param int                               $h              Image height
     * @param string                            $imgVersion     Image version: image, small_image, thumbnail
     * @return string
     */
	public function getAltImgHtml($product, $w, $h, $imgVersion='small_image', $column = 'position', $value = 1)
	{
		$product->load('media_gallery');
		if ($images = $product->getMediaGalleryImages())
		{
			$base_image = $this->_imageHelper->init($product, $this->getListingDefaultImage());
			$base_image_base_name = basename($base_image->getUrl());
			$image_array = array();
			$tmp_base_image = false;
			
			foreach($images as $tmp_image) {
				$tmp_image_base_name = basename($tmp_image->getFile());
				if(!$base_image || ($tmp_image_base_name != $base_image_base_name)){
					$image_array[] = $tmp_image;
				} elseif($tmp_image_base_name == $base_image_base_name) {
					$tmp_base_image = $tmp_image;
				}
			}
			

			$value = ((int)$value <= 0)?1:(int)$value;
			$image = false;
			if($image_array) {
				if($tmp_base_image) {
					array_unshift($image_array, $tmp_base_image);
				}
				$image = isset($image_array[$value-1])?$image_array[$value-1]:false;
			}
			if($image && $image->getUrl()){

				$imgAlt = $this->getImg($product, $w, $h, $imgVersion , $image->getFile());
				if(!$imgAlt) return '';
				return $imgAlt;
			}else{
				return '';
			}
		}
		return '';
	}
}