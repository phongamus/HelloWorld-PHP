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
        print('success')
    return event["body"]


def postToTable(primaryKey, jsonStrPayload, tableField, tableName, primaryKeyName):
    dynamodb = boto3.resource('dynamodb')
    table = dynamodb.Table(tableName)
    expression = "set "+tableField+" = :r"
    table.update_item(
        Key={
            primaryKeyName: primaryKey
        },
        UpdateExpression=expression,
        ExpressionAttributeValues={
            ':r': jsonStrPayload,
        }
    )
    print('posted the deal and ticketnum to Dynamo')

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


DEBUG = True
if(DEBUG):
    with open('samplePostToDb.json') as f:
        awsJsonBody = json.load(f)
    event = {}
    event['body'] = json.dumps(awsJsonBody)
    result = lambda_handler(event,0)

