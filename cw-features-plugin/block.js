const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl } = wp.components;
const { __ } = wp.i18n;
const { useState } = wp.element;

registerBlockType('contextual-menu/block', {
    title: __('Contextual Menu', 'contextual-menu'),
    icon: 'menu',
    category: 'layout',
    attributes: {
        levels: {
            type: 'number',
            default: 1,
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { levels } = attributes;

        return (
            <div>
                <InspectorControls>
                    <PanelBody title={__('Menu Settings', 'contextual-menu')}>
                        <RangeControl
                            label={__('Levels', 'contextual-menu')}
                            value={levels}
                            onChange={(value) => setAttributes({ levels: value })}
                            min={1}
                            max={5}
                        />
                    </PanelBody>
                </InspectorControls>
                <div className="contextual-menu">
                    <p>{__('Contextual Menu', 'contextual-menu')}</p>
                    {/* Placeholder to visualize the number of levels */}
                    {[...Array(levels)].map((_, i) => (
                        <p key={i}>{__('Level', 'contextual-menu')} {i + 1}</p>
                    ))}
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        const { levels } = attributes;
        return (
            <nav className="contextual-menu">
                <ul>
                    {/* Render the contextual menu with the specified number of levels */}
                    {[...Array(levels)].map((_, i) => (
                        <li key={i}>{__('Level', 'contextual-menu')} {i + 1}</li>
                    ))}
                </ul>
            </nav>
        );
    },
});
