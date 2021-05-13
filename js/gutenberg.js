/**
 * Modifies core blocks to add classes
 *
 * @param props
 * @param blockType
 * @param attributes
 * @returns {{className}|*}
 */

const setExtraPropsToBlockType = (props, blockType, attributes) => {
    const notDefined = (typeof props.className === 'undefined' || !props.className);

    if (blockType.name === 'core/heading') {
        return Object.assign(props, {
            className: notDefined ? `gutenberg-heading gutenberg-heading-${props.tagName}` : `gutenberg-heading gutenberg-heading-${props.tagName} ${props.className}`,
        });
    }

    if (blockType.name === 'core/list') {
        return Object.assign(props, {
            className: notDefined ? `gutenberg-list gutenberg-list-${props.tagName}` : `gutenberg-list gutenberg-list-${props.tagName} ${props.className}`,
            value: attributes.values.replace(/<li>/g, `<li class="gutenberg-list-item is-item-${props.tagName}">`),
        });
    }

    if (blockType.name === 'core/paragraph') {
        return Object.assign(props, {
            className: notDefined ? 'gutenberg-paragraph' : `gutenberg-paragraph ${props.className}`,
        });
    }

    return props;
};

wp.hooks.addFilter(
    'blocks.getSaveContent.extraProps',
    '_s/block-filters',
    setExtraPropsToBlockType
);
