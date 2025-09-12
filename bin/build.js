import * as esbuild from 'esbuild'

esbuild.build({
    entryPoints: [],
    outdir: './dist/js/',

    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    treeShaking: true,
    target: ['es2020'],
    minify: false
})
