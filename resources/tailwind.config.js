const { fontFamily, colors: defaultColors } = require('tailwindcss/defaultTheme')

const colors = {
    ...defaultColors,
    // Grayscale Design palette: https://grayscale.design/app?lums=98.00,92.00,82.00,67.00,50.00,33.00,18.00,8.00,2.00&palettes=%2319abff,%2344e869,%23ffd055,%23e82626,%23000000&filters=0%7C0,0%7C0,0%7C0,0%7C0,0%7C0&names=in,success,warning,danger,gray
    'gray': {
        '100': '#fafafa',
        '200': '#f4f4f4',
        '300': '#eaeaea',
        '400': '#d6d6d6',
        '500': '#bcbcbc',
        '600': '#9b9b9b',
        '700': '#767676',
        '800': '#505050',
        '900': '#272727'
    },
    'info': {
        '100': '#fcfeff',
        '200': '#ecf8ff',
        '300': '#d3efff',
        '400': '#a5deff',
        '500': '#61c5ff',
        '600': '#01a2ff',
        '700': '#007ac1',
        '800': '#005485',
        '900': '#002a42'
    },
    'success': {
        '100': '#f7fef9',
        '200': '#e6fceb',
        '300': '#bef7cb',
        '400': '#75ee91',
        '500': '#1bd847',
        '600': '#16b33b',
        '700': '#11892d',
        '800': '#0b5d1e',
        '900': '#062e0f'
    },
    'warning': {
        '100': '#fffefb',
        '200': '#fff4d8',
        '300': '#ffe7aa',
        '400': '#ffd059',
        '500': '#f3ae00',
        '600': '#ca9100',
        '700': '#976c00',
        '800': '#694b00',
        '900': '#332500'
    },
    'danger': {
        '100': '#fffcfc',
        '200': '#fef3f3',
        '300': '#fce6e6',
        '400': '#f9c8c8',
        '500': '#f6a9a9',
        '600': '#f07474',
        '700': '#e82525',
        '800': '#9b1010',
        '900': '#4f0808'
    },
    'brand': {
        '100': '#fdfdff',
        '200': '#f5f7ff',
        '300': '#e9edff',
        '400': '#ced7ff',
        '500': '#a8b8ff',
        '600': '#8097ff',
        '700': '#4466ff',
        '800': '#002bea',
        '900': '#00177c'
    },
}

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
                },
                brand: {
                    solid: {
                        primary: colors.brand['700'],
                        secondary: colors.brand['100'],
                        accent: {
                            primary: colors.brand['800'],
                            secondary: colors.white,
                        }
                    },
                    outline: {
                        primary: null,
                        secondary: colors.brand['800'],
                        accent: {
                            primary: colors.brand['100'],
                            secondary: colors.brand['800'],
                        }
                    },
                    minimal: {
                        primary: null,
                        secondary: colors.brand['800'],
                        accent: {
                            primary: colors.brand['100'],
                            secondary: colors.brand['800'],
                        }
                    }
                }
            }
        },
        fontFamily: {
            ...fontFamily,
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
            colors: colors
        },
    },
    variants: {},
    plugins: [
        require('@tailwindcss/custom-forms'),
        require('./css/tailwindcss/buttons.js'),
        require('./css/tailwindcss/chips.js'),
    ],
}
