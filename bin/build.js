import * as esbuild from 'esbuild'

esbuild.build({
    entryPoints: [
        './resources/js/drag_drop/alpine_components/parent.js',
        './resources/js/drag_drop/alpine_components/element.js',
        './resources/js/drag_drop/alpine_components/container.js',
        './resources/js/drag_drop/alpine_components/action.js'
    ],
    outdir: './dist/js/drag-drop',

    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    treeShaking: true,
    target: ['es2020'],
    minify: true
})
