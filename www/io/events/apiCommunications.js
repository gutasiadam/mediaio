const apiUrl = 'https://nas.arpadmedia.hu/webapi/entry.cgi';
const folderPath = '/Munka';

var sid='';
var did='';


async function getFolderData(folderPath='/') {
	folderPath = encodeURIComponent(folderPath);

	  const url='../server/synologyCommunication.php?mode=getRootFolderData&path='+folderPath;
	  try {
		const response = await fetch(url, {
		  method: 'GET',
		  headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		  },
		});
	
		if (!response.ok) {
		  throw new Error('Network response was not ok');
		}
	
		const data = await response.json(); // This line may throw an error if JSON parsing fails
		console.log(data);
	
		return data;
	  } catch (jsonError) {
		console.error('Error parsing JSON:', jsonError);
		// Handle the error caused by unsuccessful JSON parsing here
		throw jsonError; // Rethrow the error if necessary
	  }
  }
  


async function obtainAPIKey(){
	//Make an ajax request to server/synologyCommunication.php to obtain the API key

	fetch('../server/synologyCommunication.php?mode=obtainAPIKey',
	{
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	})
	//print recieved json to console
	.then(response => response.json())
	.then(data => {
		console.log(data);
		sid = data.sid;
		did = data.did;
	})
}

async function logout(){
	//Make an ajax request to server/synologyCommunication.php to logout

	fetch('../server/synologyCommunication.php?mode=logout',
	{
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	})
	//print recieved json to console

	.then(response => response.json())
	.then(data => {
		console.log(data);
	})
}