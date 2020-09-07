import { Node, Plugin } from 'tiptap'
import { nodeInputRule } from 'tiptap-commands'

const EMBED_REGEX = /^(http?s:\/\/.*)$/

export default class Iframe extends Node {
    get name () {
        return 'iframe'
    }

    get schema () {
        return {
            attrs: {
                src: {
                    default: null,
                },
            },
            group: 'block',
            selectable: false,
            draggable: false,
            parseDOM: [{
                tag: 'iframe',
                getAttrs: dom => ({
                    src: dom.getAttribute('src'),
                }),
            }],
            toDOM: node => ['iframe', {
                src: node.attrs.src,
                frameborder: 0,
                allowfullscreen: 'true',
            }],
        }
    }

    commands ({ type }) {
        return attrs => (state, dispatch) => {
            const { selection } = state
            const position = selection.$cursor ? selection.$cursor.pos : selection.$to.pos
            const node = type.create(attrs)
            const transaction = state.tr.insert(position, node)
            dispatch(transaction)
        }
    }

    inputRules ({ type }) {
        return [
            nodeInputRule(EMBED_REGEX, type, match => {
                const [src] = match
                return {
                    src,
                }
            }),
        ]
    }

    get view () {
        return {
            props: ['node', 'updateAttrs', 'view'],
            computed: {
                src: {
                    get () {
                        return this.node.attrs.src
                    },
                    set (src) {
                        this.updateAttrs({
                            src,
                        })
                    },
                },
            },
            template: `
                <div class="iframe">
                <iframe class="iframe__embed" :src="src"></iframe>
                <input form="_tiptap" class="iframe__input" @paste.stop type="text" v-model="src" v-if="view.editable"/>
                </div>
            `,
        }
    }
}
