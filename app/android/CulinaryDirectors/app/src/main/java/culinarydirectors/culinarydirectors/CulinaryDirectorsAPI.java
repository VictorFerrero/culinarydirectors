package culinarydirectors.culinarydirectors;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.HashMap;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.message.BasicNameValuePair;
   

// what are we doing for return values?
// currently just returning HttpResponse object
public class CulinaryDirectorsAPI {

	// API BASE URL
	// TODO: Dynamically switch between environments based on a config string
	private static final String localAPI = "localhost/culinarydirectors/index.php";
	
	public CulinaryDirectorsAPI() {
		
	}
// START of Org related API calls	
/*
 *$arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
 */
	public HttpResponse createOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/createOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	/*
	 * $arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
	 */
	public HttpResponse editOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/editOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//  $id = $_REQUEST['id'];
	public HttpResponse deleteOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//  $id = $_REQUEST['id'];
	public HttpResponse getOrgById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getOrgById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of Feed related API calls	
/*
 * 		$arrValues['sender'] = $_REQUEST['sender'];
		$arrValues['receiver'] = $_REQUEST['receiver'];
		$arrValues['message'] = $_REQUEST['message'];
 */
	public HttpResponse addMessage(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/addMessage";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//	 $arrValues['id'] = $_REQUEST['id']; 	
	public HttpResponse deleteMessageById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteMessageById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['senderId']
	public HttpResponse getMessagesBySenderId(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getMessagesBySenderId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['receiverId']
	public HttpResponse getMessagesByReceiverId(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getMessagesByReceiverId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['id']
	public HttpResponse getMessagesById(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getMessagesById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of menu related API calls	
    public HttpResponse createMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/createMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/editMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse createMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/createMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/editMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteMenuItem(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteMenuItem";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse createFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/createFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse editFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/editFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteFeedback(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteFeedback";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse getFeedbackForMenu(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getFeedbackForMenu";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	
// START of user related API calls	
    public HttpResponse isUserInOrg(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/isUserInOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse login(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/login";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse logout(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/logout";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse getAllUsers(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/getAllUsers";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	public HttpResponse register(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/register";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
    public HttpResponse deleteUser(HashMap<String,String> postDataHashMap) {
		List<NameValuePair> postData = this.initializePostData(postDataHashMap);
		String route = CulinaryDirectorsAPI.localAPI + "/deleteUser";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}	
	
	
// PRIVATE helper methods.	
	private List<NameValuePair> initializePostData(HashMap<String, String> postData) {
		List<NameValuePair> toReturn = new ArrayList<NameValuePair>();
		for(String key : postData.keySet()) {
			String value = postData.get(key);
			toReturn.add(new BasicNameValuePair(key,value));
		}
		return toReturn;
	}
	
	private HttpResponse sendPost(List<NameValuePair> postData, String httpPostString) {
	//	HttpClient client = new DefaultHttpClient();
		HttpClient client = HttpClientBuilder.create().build();
		HttpPost post = new HttpPost(httpPostString);
//		String output = "";
		try {
			List<NameValuePair> nameValuePairs = postData;
			post.setEntity(new UrlEncodedFormEntity(nameValuePairs));
			HttpResponse response = client.execute(post);
//			BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
//			String line = "";
//			while ((line = rd.readLine()) != null) {
//				output = output + line;
//			}
//			rd.close();
			return response;
		} catch (IOException e) {
//			output = e.getMessage();
			e.printStackTrace();
		}
		return null;
	}
	


}
