// Local QA only. Never deploy this Node server; production uses src/index.mjs
// and the real Cloudflare Access context. No production account or data is used.
import http from 'node:http';
import { DatabaseSync } from 'node:sqlite';
import { readFileSync } from 'node:fs';
import worker from '../src/index.mjs';
const sqlite = new DatabaseSync(':memory:');
sqlite.exec(readFileSync(new URL('../migrations/0001.sql', import.meta.url),'utf8'));
function prepare(query, bindings=[]) {
  return {
    bind(...values){return prepare(query,values)},
    async first(){return sqlite.prepare(query).get(...bindings)||null},
    async all(){return {success:true,results:sqlite.prepare(query).all(...bindings)}},
    async run(){return {success:true,meta:{changes:Number(sqlite.prepare(query).run(...bindings).changes)}}}
  };
}
const DB={prepare,async batch(statements){sqlite.exec('BEGIN');try {const results=[];for(const statement of statements)results.push(await statement.run());sqlite.exec('COMMIT');return results;}catch(e){sqlite.exec('ROLLBACK');throw e;}}};
const ctx={access:{aud:'gfa-local-only',getIdentity:async()=>({email:'admin@example.com'})}};
const server=http.createServer(async(req,res)=>{
  if(req.headers.host!=='127.0.0.1:8787'&&req.headers.host!=='localhost:8787'){res.writeHead(403);res.end();return;}
  const chunks=[];let size=0;for await(const chunk of req){size+=chunk.length;if(size>32768){res.writeHead(413);res.end();return;}chunks.push(chunk);}
  const request=new Request('http://'+req.headers.host+req.url,{method:req.method,headers:req.headers,...(['GET','HEAD'].includes(req.method)?{}:{body:Buffer.concat(chunks)})});
  const response=await worker.fetch(request,{DB,ACCESS_AUD:'gfa-local-only'},ctx);
  res.writeHead(response.status,Object.fromEntries(response.headers));res.end(Buffer.from(await response.arrayBuffer()));
});
server.listen(8787,'127.0.0.1',()=>console.log('Local QA: http://127.0.0.1:8787 — temporary in-memory database'));
