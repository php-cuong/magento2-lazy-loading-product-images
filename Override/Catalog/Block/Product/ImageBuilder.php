<?php
/**
 * GiaPhuGroup Co., Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GiaPhuGroup.com license that is
 * available through the world-wide-web at this URL:
 * https://www.giaphugroup.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    PHPCuong
 * @package     PHPCuong_LazyLoadingImage
 * @copyright   Copyright (c) 2019-2020 GiaPhuGroup Co., Ltd. All rights reserved. (http://www.giaphugroup.com/)
 * @license     https://www.giaphugroup.com/LICENSE.txt
 */

namespace PHPCuong\LazyLoadingImage\Override\Catalog\Block\Product;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        /** @var \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface $simpleOption */
        $simpleOption = $this->product->getOptionById('simple_product');

        if ($simpleOption !== null) {
            $optionProduct = $simpleOption->getProduct();
            $this->setProduct($optionProduct);
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'PHPCuong_LazyLoadingImage::product/image.phtml'
            : 'PHPCuong_LazyLoadingImage::product/image_with_borders.phtml';

        try {
            $imagesize = $helper->getResizedImageInfo();
        } catch (NotLoadInfoImageException $exception) {
            $imagesize = [$helper->getWidth(), $helper->getHeight()];
        }

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => $imagesize[0],
                'resized_image_height' => $imagesize[1],
                'product_id' => $this->product->getId()
            ],
        ];

        return $this->imageFactory->create($data);
    }
}
