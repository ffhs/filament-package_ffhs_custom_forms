import * as esbuild from 'esbuild'

esbuild.build({
    entryPoints: ['./resources/js/drag_drop_parent.js', './resources/js/drag_drop_element.js'],
    outdir: './dist',

    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    treeShaking: true,
    target: ['es2020'],
    //minify: true
})
