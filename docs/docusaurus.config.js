module.exports = {
    title: 'Fastlane Documentation',
    tagline: 'Get up and running with the CMS built with customization in mind.',
    url: 'https://docs.cbtech.co.uk',
    baseUrl: '/fastlane/',
    favicon: 'img/favicon.ico',
    organizationName: 'cbtech-ltd', // Usually your GitHub org/user name.
    projectName: 'fastlane', // Usually your repo name.
    themeConfig: {
        navbar: {
            title: 'Fastlane',
            logo: {
                alt: 'Fastlane Logo',
                src: 'img/logo.svg',
            },
            links: [
                {
                    to: 'docs/',
                    activeBasePath: 'docs',
                    label: 'Docs',
                    position: 'left',
                },
                {
                    href: 'https://cbtech.co.uk',
                    label: 'CBTECH',
                    position: 'right',
                },
                {
                    href: 'https://github.com/cbtech-ltd/fastlane',
                    label: 'GitHub',
                    position: 'right',
                },
            ],
        },
        footer: {
            style: 'dark',
            links: [
                {
                    title: 'Community',
                    items: [
                        {
                            label: 'Stack Overflow',
                            href: 'https://stackoverflow.com/questions/tagged/fastlanecms',
                        },
                    ],
                },
                {
                    title: 'More',
                    items: [
                        {
                            label: 'GitHub',
                            href: 'https://github.com/cbtech-ltd/fastlane',
                        },
                    ],
                },
            ],
            copyright: `Copyright © ${new Date().getFullYear()} CBTECH LTD. Built with Docusaurus.`,
        },
    },
    presets: [
        [
            '@docusaurus/preset-classic',
            {
                docs: {
                    // It is recommended to set document id as docs home page (`docs/` path).
                    homePageId: 'about',
                    sidebarPath: require.resolve('./sidebars.js'),
                    // Please change this to your repo.
                    editUrl:
                        'https://github.com/cbtech-ltd/fastlane/edit/develop/docs/',
                },
                theme: {
                    customCss: require.resolve('./src/css/custom.css'),
                },
            },
        ],
    ],
}
