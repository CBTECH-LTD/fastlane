const forEach = require('lodash/forEach')
const plugin = require('tailwindcss/plugin')

module.exports = plugin(function ({ addComponents, theme, config }) {
    function addButton (modifier, options) {
        addComponents({
            [`.btn${modifier}`]: options
        })
    }

    function makeColorConfigFromTheme (color) {
        return {
            solid: {
                primary: color['500'],
                secondary: theme('colors.white'),
                accent: {
                    primary: color['600'],
                    secondary: theme('colors.white'),
                },
            },
            outline: {
                primary: null,
                secondary: color['500'],
                accent: {
                    primary: color['100'],
                    secondary: color['600'],
                }
            },
            minimal: {
                primary: null,
                secondary: color['500'],
                accent: {
                    primary: color['100'],
                    secondary: color['600'],
                }
            }
        }
    }

    // Add base style...
    addButton('', {
        position: 'relative',
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
        boxShadow: theme('boxShadow.none'),
        borderStyle: 'solid',
        borderWidth: theme('borderWidth.default'),
        borderRadius: theme('borderRadius.default'),
        transition: 'all 150ms ease-in-out',
        '.btn-icon': {
            opacity: '0.6',
            transition: 'opacity 150ms ease-in-out',
            display: 'flex',
            alignItems: 'center'
        },
        '.btn-icon-l': {
            marginRight: theme('margin.2'),
        },
        '.btn-icon-r': {
            marginLeft: theme('margin.2'),
        },
        '&.btn-disabled': {
            pointerEvent: 'none',
            opacity: '0.5',
        },
        '&.btn-loading': {},
        '&:hover:not([disabled])': {
            backgroundColor: theme('colors.brand.600'),
            boxShadow: theme('boxShadow.lg'),
            '.btn-icon': {
                opacity: '1',
            }
        },
        '&:focus:not([disabled])': {
            boxShadow: theme('boxShadow.outline'),
            outline: 'none',
            '.btn-icon': {
                opacity: '1',
            }
        },
        '&:active:not([disabled])': {
            '.btn-icon': {
                opacity: '1',
            }
        },
        '&:disabled, &[disabled], &.btn-disabled': {
            boxShadow: 'none'
        },
        '&.btn-oval': {
            borderWidth: theme('borderWidth.full'),
            borderRadius: theme('borderRadius.full'),
        }
    })

    // Add size variants...
    addButton('-sm', {
        padding: `${theme('padding.1')} ${theme('padding.2')}`,
        fontSize: theme('fontSize.xs'),
        fontWeight: theme('fontWeight.normal'),
        '.btn-icon': {
            fontSize: theme('fontSize.lg')
        }
    })

    addButton('-base', {
        padding: `${theme('padding.1')} ${theme('padding.2')}`,
        fontSize: theme('fontSize.sm'),
        fontWeight: theme('fontWeight.medium'),
        '.btn-icon': {
            fontSize: theme('fontSize.xl')
        }
    })

    addButton('-lg', {
        padding: `${theme('padding.2')} ${theme('padding.3')}`,
        fontSize: theme('fontSize.base'),
        fontWeight: theme('fontWeight.medium'),
        '.btn-icon': {
            fontSize: theme('fontSize.xl')
        }
    })

    addButton('-xl', {
        padding: `${theme('padding.4')} ${theme('padding.6')}`,
        fontSize: theme('fontSize.xl'),
        fontWeight: theme('fontWeight.semibold'),
        '.btn-icon': {
            fontSize: theme('fontSize.xl')
        }
    })

    // Add color variants...
    let colors = {}

    forEach(theme('colors'), (value, name) => {
        colors[name] = makeColorConfigFromTheme(value)
    })

    let customColors = theme('buttons.colors')
    if (customColors) {
        colors = { ...colors, ...customColors }
    }

    forEach(colors, (color, colorName) => {
        // Solid
        addButton(`-${colorName}-solid`, {
            backgroundColor: color.solid.primary,
            color: color.solid.secondary,
            borderColor: color.solid.primary,
            '&:hover:not([disabled])': {
                backgroundColor: color.solid.accent.primary,
                borderColor: color.solid.accent.primary,
            },
            '&:focus:not([disabled])': {
                backgroundColor: color.solid.accent.primary,
                borderColor: color.solid.accent.primary,
            },
            '&:active:not([disabled])': {
                backgroundColor: color.solid.accent.primary,
                borderColor: color.solid.accent.primary,
            },
            '&:disabled, &[disabled]': {
                backgroundColor: theme('colors.gray.300'),
                color: theme('colors.gray.500'),
                borderColor: theme('colors.gray.300'),
                boxShadow: 'none',
                pointerEvents: 'none',
            },
        })

        // Outline
        addButton(`-${colorName}-outline`, {
            backgroundColor: 'transparent',
            borderColor: color.outline.secondary,
            color: color.outline.secondary,
            '&:hover:not([disabled])': {
                backgroundColor: color.outline.accent.primary,
                borderColor: color.outline.accent.secondary,
                color: color.outline.accent.secondary,
            },
            '&:focus:not([disabled])': {
                backgroundColor: color.outline.accent.primary,
                borderColor: color.outline.accent.secondary,
                color: color.outline.accent.secondary,
            },
            '&:active:not([disabled])': {
                backgroundColor: color.outline.accent.primary,
                borderColor: color.outline.accent.secondary,
                color: color.outline.accent.secondary,
            },
            '&:disabled, &[disabled]': {
                backgroundColor: 'transparent',
                color: theme('colors.gray.400'),
                borderColor: theme('colors.gray.400'),
                boxShadow: 'none'
            },
        })

        // Minimal
        addButton(`-${colorName}-minimal`, {
            backgroundColor: 'transparent',
            borderColor: 'transparent',
            boxShadow: 'none',
            color: color.minimal.secondary,
            '&:hover:not([disabled])': {
                backgroundColor: color.minimal.accent.primary,
                boxShadow: 'none',
                color: color.minimal.accent.secondary,
            },
            '&:focus:not([disabled])': {
                backgroundColor: color.minimal.accent.primary,
                boxShadow: 'none',
                color: color.minimal.accent.secondary,
            },
            '&:active:not([disabled])': {
                backgroundColor: color.minimal.accent.primary,
                boxShadow: 'none',
                color: color.minimal.accent.secondary,
            },
            '&:disabled, &[disabled]': {
                backgroundColor: 'transparent',
                color: theme('colors.gray.400'),
            },
        })
    })
}, {
    theme: {
        buttons: {
            colors: {}
        }
    }
})
