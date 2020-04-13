/**
 * External dependencies
 */
import classNames from 'classnames';

//  Import CSS.
import './style.scss'
import './editor.scss'

// Import icons
import icons from './icons';

const {__} = wp.i18n;
const {InspectorControls} = wp.blockEditor;
const {Fragment, RawHTML} = wp.element;
const {registerBlockType} = wp.blocks;
const {TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow} = wp.components;

registerBlockType('bokun/product-list-widget', {
    title: __('Bokun product list'),
    category: 'widgets',
    keywords: [
        __('bokun'),
    ],

    // Enable or disable support for low-level features
    supports: {
        html: false,
        reusable: true,
        align: ['full', 'wide']
    },

    // Set up data model for custom block
    attributes: {
        bookingChannelId: {
            type: 'string'
        },
        productListId: {
            type: 'string'
        },
        paddingSize: {
            type: 'string'
        },
    },

    // The UI for the WordPress editor
    edit: props => {
        // Pull out the props we'll use
        const {attributes, className, setAttributes} = props;


        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody>
                        <PanelRow>
                            <TextControl
                                value={attributes.productListId}
                                type="number"
                                onChange={value => setAttributes({productListId: value})}
                                placeholder="Product list ID"
                                label="Product list ID"
                            />
                        </PanelRow>
                        <PanelRow>
                            <TextControl
                                value={attributes.bookingChannelId}
                                type="text"
                                onChange={value => setAttributes({bookingChannelId: value})}
                                placeholder="Booking channel ID"
                                label="Booking channel ID"
                            />
                        </PanelRow>
                        <PanelRow>
                            <SelectControl
                                label={__('Padding')}
                                value={ attributes.paddingSize }
                                options={ [
                                    { label: 'No padding', value: 'no-padding' },
                                    { label: 'Small padding', value: 'small-padding' },
                                    { label: 'Medium padding', value: 'medium-padding' },
                                    { label: 'Large padding', value: 'large-padding' },
                                ] }
                                onChange={ value => setAttributes( { paddingSize: value } ) }
                            />
                        </PanelRow>
                    </PanelBody>
                </InspectorControls>
                <div className={classNames(className, attributes.paddingSize)}>
                    {icons.bokunWhiteLogo}
                    {(!attributes.bookingChannelId || !attributes.productListId) &&
                    <p className={'wp-block-bokun-product-widget__warning'}>{__('When using default widget you need to define booking channel and bokun product id in block settings!')}</p>}
                    {(attributes.bookingChannelId && attributes.productListId) &&
                    <p>{__('Your default widget is ready. Just preview the page and see it in action.')}</p>}
                </div>
            </Fragment>
        )
    },

    save: props => {
        //output via PHP
        return null;
    }
});