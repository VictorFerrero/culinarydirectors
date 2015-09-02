package culinarydirectors.culinarydirectors;

/**
 * Created by Sreenath on 9/1/2015.
 */

import java.util.HashMap;
import java.util.concurrent.Callable;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.Future;
import org.apache.http.HttpResponse;
import org.json.JSONObject;

public class APIThreadPool {
    //properties
    private static final ExecutorService pool = Executors.newCachedThreadPool();
    //singleton
    private static APIThreadPool cachedThreadPool = null;
    // API object
    private static CulinaryDirectorsAPI api = new CulinaryDirectorsAPI();
    
    protected APIThreadPool() {
        //defeat instantiation!
    }
    public static APIThreadPool getInstance() {
        // Obtain a cached thread pool
        if (cachedThreadPool == null) {
            cachedThreadPool = new APIThreadPool();
        }
        return cachedThreadPool;
    }
    //no destructors in java but in case we need to specify some sort of explicit cleaning function
    public void destroy(){
        APIThreadPool.pool.shutdown(); // shutdown the pool.
        cachedThreadPool = null;
    }
    //functions
    //TODO: make class for logging info on call, return that class instead of String
    // JSON object will either contain data requested (user, menu) or it will have
    // fields denoting success/ failure api call
    //TODO: pass in params that API calling functions in CulinaryDirectorsAPI needs?
    
    // String route = /menu/createMenu
    public JSONObject callAPIAsync(final HashMap<String,String> postDataHashMap, final String route){

        //TODO: Run api call, set some return values (api return, statuses, etc)
        Callable<JSONObject> aCallable = new Callable<JSONObject>(){
            @Override
            public JSONObject call() {
                try{
                  HttpResponse response = APIThreadPool.api.callAPI(postDataHashMap, route);
                  JSONObject json = api.getJSONfromResponse(response);
                  return json;
                }catch(Exception e){
					//TODO: return error json
                }
            }
        };
        // Time to run it
        Future<JSONObject> callableFuture = APIThreadPool.pool.submit(aCallable);
        try {
            // get() waits for the task to finish and then gets the result
            return callableFuture.get();
        } catch (InterruptedException e) {
            // thrown if task was interrupted before completion
            e.printStackTrace();
        } catch (ExecutionException e) {
            // thrown if the task threw an execption while executing
            e.printStackTrace();
        }
        return new JSONObject();
    }



}
