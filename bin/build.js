import * as esbuild from 'esbuild'

esbuild.build({
    entryPoints: [
        './resources/js/alpine_components/drag_drop_parent.js',
        './resources/js/alpine_components/drag_drop_element.js',
        './resources/js/alpine_components/drag_drop_container.js',
        './resources/js/alpine_components/drag_drop_action.js'
    ],
    outdir: './dist',

    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    treeShaking: true,
    target: ['es2020'],
   // minify: true
})
