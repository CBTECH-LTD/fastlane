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
        default: '#61c5ff',
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
        default: '#1bd847',
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
        default: '#f3ae00',
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
        default: '#E82626',
        '100': '#FCDFDF',
        '200': '#F7B1B1',
        '300': '#F28282',
        '400': '#ED5454',
        '500': '#E82626',
        '600': '#C61515',
        '700': '#981010',
        '800': '#6A0B0B',
        '900': '#3C0606'
    },
    'brand': {
        default: '#2081CF',
        '100': '#C6E0F6',
        '200': '#9AC9EF',
        '300': '#6DB1E8',
        '400': '#4199E1',
        '500': '#2081CF',
        '600': '#1965A3',
        '700': '#134A77',
        '800': '#0C2F4B',
        '900': '#05131F'
    },
}

module.exports = {
    theme: {
        customForms: theme => ({
            default: {
                input: {
                    boxShadow: theme('boxShadow.sm')
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
                            primary: colors.gray['200'],
                            secondary: colors.gray['900'],
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
