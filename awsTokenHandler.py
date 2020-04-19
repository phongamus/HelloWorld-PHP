import json
import boto3

checklist = [
    'refreshTokenKey', 
    'QBORealmID', 
    'x_refresh_token_expires_in'
]


def lambda_handler(event, context):
    # TODO implement
    print(event)
    
    jsonStrPayload = event["body"]
    jsonPayload = json.loads(jsonStrPayload)
    if(checkValidJson(jsonPayload, checklist)):
        result = postToTable(jsonPayload['QBORealmID'],jsonStrPayload, 'credentials', 'qboCredential', 'mid')
        responseStatus = (result['ResponseMetadata']['HTTPStatusCode'])
    else:
        return 'something is missing'
    if responseStatus == 200:
        print("Table Updated Sucessfully")
        return 'Table Updated Sucessfully'
    else:
        return 'something when wrong with updating table'


def postToTable(primaryKey, jsonStrPayload, tableField, tableName, primaryKeyName):
    dynamodb = boto3.resource('dynamodb')
    table = dynamodb.Table(tableName)
    expression = "set "+tableField+" = :r"
    response = table.update_item(
        Key={
            primaryKeyName: primaryKey
        },
        UpdateExpression=expression,
        ExpressionAttributeValues={
            ':r': jsonStrPayload,
        }
    )
    return response

    print('posted item to table')

def parseJson(jsonObj):
    pass

def checkValidJson(messageJsonAsDict, checklist):
    notFoundItems = 0
    for item in checklist:
        if ( item not in messageJsonAsDict):
            print(item + " not found")
            notFoundItems +=1
    if notFoundItems > 0:
        print ( 'items not found: ' + str(notFoundItems))
        return False
    else:
        print('All items Found')
        return True

# Do this if running locally
if __name__ == "__main__":
    with open('samplePostToDb.json') as f:
        awsJsonBody = json.load(f)
    event = {}
    event['body'] = json.dumps(awsJsonBody)
    result = lambda_handler(event,0)

