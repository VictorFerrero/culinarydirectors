package culinarydirectors.culinarydirectors;

/**
 * Created by Sreenath on 9/1/2015.
 */
import android.util.Log;

import java.util.concurrent.Callable;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.Future;

public class APIThreadPool {
    //properties
    private static final ExecutorService pool = Executors.newCachedThreadPool();
    //singleton
    private static APIThreadPool cachedThreadPool = null;
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
    //TODO: pass in params that API calling functions in CulinaryDirectorsAPI needs?
    public String callAPIAsync(){

        // Set up Good Ol' Runnable
        //TODO: Run api call, set some return values (api return, statuses, etc)
        Runnable aRunnable = new Runnable(){
            @Override
            public void run() {
                try{
                    //SKP: Replace this loop with API CALL -- used to test that thread runs
                    // successfully and in parallel to UI Loading. Test successful 9/1
                    for(int i = 0; i < 10; i++){
                        Log.w("APITHREADPOOL","Runnable is doing something");
                        Thread.sleep(1000);
                    }
                }catch(Exception e){

                }
            }
        };

        // Time to run it
        Future<?> runnableFuture = APIThreadPool.pool.submit(aRunnable);

        return "";
    }



}
