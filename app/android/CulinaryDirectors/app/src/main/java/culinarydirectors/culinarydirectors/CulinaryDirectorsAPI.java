//package culinarydirectors.culinarydirectors;

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
   

public class CulinaryDirectorsAPI {

	// API BASE URL
	// TODO: Dynamically switch between environments based on a config string
	private static final String localAPI = "http://54.186.148.207/culinarydirectors/index.php";
	
	public CulinaryDirectorsAPI() {
		
	}
// START of Org related API calls	
	public HttpResponse createOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/org/createOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}

	public HttpResponse editOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/org/editOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}

	public HttpResponse deleteOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/org/deleteOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getOrgById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/org/getOrgById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of Feed related API calls	

	public HttpResponse addMessage(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/feed/addMessage";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse deleteMessageById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/feed/deleteMessageById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getMessagesBySenderId(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/feed/getMessagesBySenderId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getMessagesByReceiverId(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/feed/getMessagesByReceiverId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getMessagesById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/feed/getMessagesById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of menu related API calls	
    public HttpResponse createMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/createMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/editMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/deleteMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse createMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/createMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/editMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/deleteMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse createFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/createFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/editFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/deleteFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse getFeedbackForMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/menu/getFeedbackForMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	
// START of user related API calls	
    public HttpResponse isUserInOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/isUserInOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse login(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/login";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse logout(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/logout";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getAllUsers(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/getAllUsers";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse register(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/register";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteUser(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/user/deleteUser";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}	
    
    /**
     * 		Extracts JSON from the HttpResponse.
     * 
     * 		TODO: return a JSON object instead of a string that conforms to JSON standards
     * 		http://developer.android.com/reference/org/json/JSONObject.html
     * */
    public String getJSONfromResponse(HttpResponse response) throws IOException {
    	BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
		String line = "";
		String output = "";
		while ((line = rd.readLine()) != null) {
			output = output + line;
		}
		rd.close();
		return output;
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
