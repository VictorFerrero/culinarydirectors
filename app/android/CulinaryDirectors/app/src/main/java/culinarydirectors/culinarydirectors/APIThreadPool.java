package culinarydirectors.culinarydirectors;

/**
 * Created by Sreenath on 9/1/2015.
 */

import android.app.Activity;

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
    private static final CulinaryDirectorsAPI api = new CulinaryDirectorsAPI();
    
    protected APIThreadPool() {
        //defeat instantiation!
    }
    public static APIThreadPool getInstance() {
        // Obtain a cached thread pool
        if (cachedThreadPool == null) { cachedThreadPool = new APIThreadPool(); }
        return cachedThreadPool;
    }
    //no destructors in java but in case we need to specify some sort of explicit cleaning function
    public void destroy(){
        APIThreadPool.pool.shutdown();
        cachedThreadPool = null;
    }

    public void callAPIAsync
    (final HashMap<String,String> postDataHashMap, final String route, final String callback, final Activity activity){
        Runnable aRunnable = new Runnable(){
            @Override
            public void run() {
                try{
                    HttpResponse response = APIThreadPool.api.callAPI(postDataHashMap, route);
                    JSONObject json = api.getJSONfromResponse(response);
                    api.runCallback(callback, json, activity);
                }catch(Exception e){
                    //TODO: error handling
                }
            }
        };
        // Time to run it -- keep future object reference for debugging purposes
        Future<?> callableFuture = APIThreadPool.pool.submit(aRunnable);
    }
}