const { colors } = require('tailwindcss/defaultTheme')

module.exports = {
    theme: {
        customForms: theme => ({
            default: {
                input: {
                    boxShadow: theme('boxShadow.default')
                }
            }
        }),
        buttons: {
            colors: {
                light: {
                    solid: {
                        primary: colors.gray['100'],
                        secondary: colors.gray['800'],
                        accent: {
                            primary: colors.gray['200'],
                            secondary: colors.gray['800'],
                        }
                    },
                    outline: {
                        primary: null,
                        secondary: colors.gray['800'],
                        accent: {
                            primary: colors.gray['200'],
                            secondary: colors.gray['800'],
                        }
                    },
                    minimal: {
                        primary: null,
                        secondary: colors.gray['800'],
                        accent: {
                            primary: colors.gray['200'],
                            secondary: colors.gray['800'],
                        }
                    }
                },
                black: {
                    solid: {
                        primary: colors.gray['900'],
                        secondary: colors.white,
                        accent: {
                            primary: colors.black,
                            secondary: colors.white,
                        }
                    },
                    outline: {
                        primary: null,
                        secondary: colors.gray['900'],
                        accent: {
                            primary: colors.gray['100'],
                            secondary: colors.black,
                        }
                    },
                    minimal: {
                        primary: null,
                        secondary: colors.gray['900'],
                        accent: {
                            primary: colors.gray['100'],
                            secondary: colors.black,
                        }
                    }
                }
            }
        },
        fontFamily: {
            sans: [
                'Inter',
                '"Segoe UI"',
                'Roboto',
                '"Helvetica Neue"',
                'Arial',
                'sans-serif',
            ],
        },
        extend: {
            colors: {
                success: colors.green,
                danger: colors.red,
                warning: colors.yellow,
                info: colors.blue,
                gray: {
                    100: '#f7fcfd',
                    200: '#f2f5fa',
                    300: '#edf2f7',
                    // 400: '#e2e8f0',
                    400: '#cbd5e0',
                    500: '#a0aec0',
                    600: '#718096',
                    700: '#4a5568',
                    800: '#2d3748',
                    900: '#1a202c',
                },
                brand: {
                    100: '#EAEBED',
                    200: '#CBCDD1',
                    300: '#ABAFB6',
                    400: '#6C737F',
                    500: '#2D3748',
                    600: '#293241',
                    700: '#1B212B',
                    800: '#141920',
                    900: '#0E1116',
                },
            }
        },
    },
    variants: {},
    plugins: [
        require('@tailwindcss/custom-forms'),
        require('./css/tailwindcss/buttons.js'),
        require('./css/tailwindcss/chips.js'),
    ],
}
