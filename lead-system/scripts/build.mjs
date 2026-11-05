import { pathToFileURL } from 'node:url';
import { mkdir, readFile } from 'node:fs/promises';
import path from 'node:path';
const { build } = await import(process.env.GFA_ESBUILD_PATH ? pathToFileURL(process.env.GFA_ESBUILD_PATH).href : 'esbuild');
await mkdir('dist', {recursive:true});
for (const [entry, output] of [['src/index.mjs','dist/worker.mjs'],['src/capture.mjs','dist/capture.mjs']]) {
  await build({entryPoints:[path.resolve(entry)],outfile:output,bundle:true,format:'esm',platform:'browser',target:'es2022',minify:false,legalComments:'none',plugins:[{
    name:'local-esm-only',
    setup(builder){
      builder.onResolve({filter:/.*/},args=>{
        if(args.importer && !args.path.startsWith('.')) throw new Error('Only local ESM imports are supported.');
        return {path:path.resolve(args.importer?path.dirname(args.importer):process.cwd(),args.path),namespace:'gfa'};
      });
      builder.onLoad({filter:/.*/,namespace:'gfa'},async args=>({contents:await readFile(args.path,'utf8'),loader:'js'}));
    }
  }]});
  console.log(`Built ${output}`);
}
