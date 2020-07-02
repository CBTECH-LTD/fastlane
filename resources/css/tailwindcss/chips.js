const forEach = require('lodash/forEach')

module.exports = function ({ addComponents, theme }) {
    function addChip(modifier, options) {
        addComponents({
            [`.chip${modifier}`]: options
        })
    }

    // Add base style...
    addChip('', {
        position: 'relative',
        borderStyle: 'solid',
        borderWidth: '1px',
        borderRadius: theme('borderRadius.full'),
    })

    // Text variants...
    addChip('-upper', {
        textTransform: 'uppercase',
    })

    // Add size variants...
    addChip('-xs', {
        fontWeight: theme('fontWeight.medium'),
        fontSize: theme('fontSize.xs'),
        padding: '0.1rem 0.25rem',
    })

    addChip('-sm', {
        fontWeight: theme('fontWeight.medium'),
        fontSize: theme('fontSize.sm'),
        padding: '0.1rem 0.25rem',
    })

    addChip('-base', {
        fontWeight: theme('fontWeight.medium'),
        fontSize: theme('fontSize.base'),
        padding: '0.2rem 0.25rem',
    })

    addChip('-lg', {
        fontWeight: theme('fontWeight.medium'),
        fontSize: theme('fontSize.lg'),
        padding: '0.3rem 0.4rem',
    })

    // Color variants...
    forEach(theme('colors'), (colorValues, colorName) => {
        addChip(`-${colorName}-solid`, {
            backgroundColor: colorValues['300'],
            borderColor: colorValues['300'],
            color: colorValues['700'],
        })

        addChip(`-${colorName}-outline`, {
            backgroundColor: 'transparent',
            borderColor: colorValues['700'],
            color: colorValues['700'],
        })
    })
}
