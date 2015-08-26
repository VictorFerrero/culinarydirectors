import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
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

	// base urls for each controller
	private static final String MenuControllerRoute = "";
	private static final String OrgControllerRoute = "";
	private static final String UserControllerRoute = "";
	private static final String FeedControllerRoute = "";
	
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
	public HttpResponse createOrg(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.OrgControllerRoute + "/createOrg";
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
	public HttpResponse editOrg(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.OrgControllerRoute + "/editOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//  $id = $_REQUEST['id'];
	public HttpResponse deleteOrg(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.OrgControllerRoute + "/deleteOrg";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//  $id = $_REQUEST['id'];
	public HttpResponse getOrgById(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.OrgControllerRoute + "/getOrgById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of Feed related API calls	
/*
 * 		$arrValues['sender'] = $_REQUEST['sender'];
		$arrValues['receiver'] = $_REQUEST['receiver'];
		$arrValues['message'] = $_REQUEST['message'];
 */
	public HttpResponse addMessage(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.FeedControllerRoute + "/addMessage";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
//	 $arrValues['id'] = $_REQUEST['id']; 	
	public HttpResponse deleteMessageById(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.FeedControllerRoute + "/deleteMessageById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['senderId']
	public HttpResponse getMessagesBySenderId(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.FeedControllerRoute + "/getMessagesBySenderId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['receiverId']
	public HttpResponse getMessagesByReceiverId(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.FeedControllerRoute + "/getMessagesByReceiverId";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
	// $arrValues['id'] = $_REQUEST['id']
	public HttpResponse getMessagesById(String[] name, String[] value) {
		List<NameValuePair> postData = this.initializePostData(name, value);
		String route = CulinaryDirectorsAPI.FeedControllerRoute + "/getMessagesById";
		HttpResponse response = this.sendPost(postData, route);
		return response;
	}
	
// START of menu related API calls	
	
	
	
// START of user related API calls	
	
	

		
// PRIVATE helper methods.	
	private List<NameValuePair> initializePostData(String[] name, String[] value) {
		List<NameValuePair> postData = new ArrayList<NameValuePair>();
		for(int i = 0; i < name.length; i++) {
			postData.add(new BasicNameValuePair(name[i],value[i]));
		}
		return postData;
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
