mod.wizards {
    newContentElement {
        wizardItems {
            magelink {
                header = MageLink
                elements {
                    tx_magelink_productdisplay {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/products.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_product_integration
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_product_integration_description
						tt_content_defValues {
							CType = list
							list_type = magelink_productdisplay
						}
					}
					
					tx_magelink_categorydisplay {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/categories.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_category_integration
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_category_integration_description
						tt_content_defValues {
							CType = list
							list_type = magelink_categorydisplay
						}
					}
					
					tx_magelink_cartdisplay {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/cart.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_cart_integration
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_cart_integration_description
						tt_content_defValues {
							CType = list
							list_type = magelink_cartdisplay
						}
					}
					
					tx_magelink_blockdisplay {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/block.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_block_integration
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_block_integration_description
						tt_content_defValues {
							CType = list
							list_type = magelink_blockdisplay
						}
					}
					
					tx_magelink_loginform {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/login.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_single_sign_on
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_single_sign_on_description
						tt_content_defValues {
							CType = list
							list_type = magelink_loginform
						}
					}
					
					tx_magelink_forgotpasswordform {
						icon = ../typo3conf/ext/magelink/Resources/Public/Icons/Plugins/forgot_password.gif
						title = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_forgot_password
						description = LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_forgot_password_description
						tt_content_defValues {
							CType = list
							list_type = magelink_forgotpasswordform
						}
					}	
                }
                show = *
            }
        }
    }
}