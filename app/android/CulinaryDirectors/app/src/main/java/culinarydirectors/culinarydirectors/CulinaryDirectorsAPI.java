package culinarydirectors.culinarydirectors;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;


public class CulinaryDirectorsAPI {

	// API BASE URL
	// TODO: Dynamically switch between environments based on a config string
	private static final String localAPI = "http://54.186.148.207/culinarydirectors/index.php";
	
	public CulinaryDirectorsAPI() {
		
	}

	public HttpResponse callAPI(HashMap<String,String> postDataHashMap, String route) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		route = CulinaryDirectorsAPI.localAPI + route;
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
    /**
     * 		Extracts JSON from the HttpResponse.
     *
     * */
    public JSONObject getJSONfromResponse(HttpResponse response) throws IOException, JSONException {
    	BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
		String line = "";
		String output = "";
		while ((line = rd.readLine()) != null) {
			output = output + line;
		}
		rd.close();
		output = output.substring(1, output.length() - 1);
		JSONObject toReturn = new JSONObject(output);
		return toReturn;
    }
	
// PRIVATE helper methods.	
	/**
	*	Extracts the name value pairs from the HashMap and buils a List<NameValuePair> 
	* 	which will be used by HttpPost object in this.sendPost. 
	* */
	private List<NameValuePair> initializePostData(HashMap<String, String> postData) {
		List<NameValuePair> toReturn = new ArrayList<NameValuePair>();
		for(String key : postData.keySet()) {
			String value = postData.get(key);
			toReturn.add(new BasicNameValuePair(key,value));
		}
		return toReturn;
	}
	
	/**
	 * 		Sends an HttpPost with the data in postData to the
	 * 		specified route
	 * 
	 * */
	private HttpResponse sendPost(List<NameValuePair> postData, String route) {
		HttpClient client = HttpClientBuilder.create().build();
		HttpPost post = new HttpPost(route);
		try {
			post.setEntity(new UrlEncodedFormEntity(postData));
			HttpResponse response = client.execute(post);
			return response;
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}
}
