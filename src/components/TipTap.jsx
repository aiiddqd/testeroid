import React, {useState,useEffect} from 'react'
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
// import { RichTextEditor, Link } from '@mantine/tiptap';
// import Highlight from '@tiptap/extension-highlight';
// import Underline from '@tiptap/extension-underline';
// import TextAlign from '@tiptap/extension-text-align';
// import Superscript from '@tiptap/extension-superscript';
// import SubScript from '@tiptap/extension-subscript';
import * as db from '../includes/db'




export default function TipTap() {

    // let content = db.get('default');

    const [content, setContent] = useState();


    useEffect( () => {
        async function fetchData() {
            let data = db.get('default');
            setContent(data);
            // editor.commands.setContent(data)
            console.log(content);

        }
        fetchData();
        // editor.commands.setContent(`<p>Example Text</p>`)
        
    }, []);

 
    const editor = useEditor({
        extensions: [
            StarterKit,
        ],
        onUpdate({ editor }) {
            db.set('default', editor.getText());
        },
        content: 'HW',
    })
    // editor.commands.setContent(`<p>Example Text</p>`)

    return (
        <EditorContent editor={editor} />
    );
}