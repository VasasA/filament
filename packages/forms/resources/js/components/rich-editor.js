import { Editor } from '@tiptap/core'
import Blockquote from '@tiptap/extension-blockquote'
import Bold from '@tiptap/extension-bold'
import BulletList from '@tiptap/extension-bullet-list'
import Code from '@tiptap/extension-code'
import CodeBlock from '@tiptap/extension-code-block'
import Document from '@tiptap/extension-document'
import Heading from '@tiptap/extension-heading'
import History from '@tiptap/extension-history'
import Italic from '@tiptap/extension-italic'
import Link from '@tiptap/extension-link'
import ListItem from '@tiptap/extension-list-item'
import OrderedList from '@tiptap/extension-ordered-list'
import Paragraph from '@tiptap/extension-paragraph'
import Strike from '@tiptap/extension-strike'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import Text from '@tiptap/extension-text'
import Underline from '@tiptap/extension-underline'

export default function richEditorFormComponent({ state }) {
    let editor

    return {
        state,

        shouldUpdateState: true,

        updatedAt: Date.now(),

        init: function () {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    Blockquote,
                    Bold,
                    BulletList,
                    Code,
                    CodeBlock,
                    Document,
                    Heading,
                    History,
                    Italic,
                    Link,
                    ListItem,
                    OrderedList,
                    Paragraph,
                    Strike,
                    Subscript,
                    Superscript,
                    Text,
                    Underline,
                ],
                content: this.state,
            })

            editor.on('create', ({ editor }) => {
                this.updatedAt = Date.now()
            })

            editor.on('update', ({ editor }) => {
                this.updatedAt = Date.now()

                this.state = editor.getJSON()

                this.shouldUpdateState = false
            })

            editor.on('selectionUpdate', ({ editor }) => {
                this.updatedAt = Date.now()
            })

            this.$watch('state', () => {
                if (! this.shouldUpdateState) {
                    this.shouldUpdateState = true

                    return
                }

                editor.commands.setContent(this.state)
            })
        },

        getEditor: function () {
            return editor
        },

    }
}
