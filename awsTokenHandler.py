import json
import boto3

def lambda_handler(event, context):
    # TODO implement
    print(event)
    
    jsonPayload = json.loads(event["body"])
    print(jsonPayload)
    return event["body"]


def postToTable(primaryKey, jsonPayload, tableField, tableName, primaryKeyName):
    dynamodb = boto3.resource('dynamodb')
    table = dynamodb.Table(tableName)
    expression = "set "+tableField+" = :r"
    table.update_item(
        Key={
            primaryKeyName: primaryKey
        },
        UpdateExpression=expression,
        ExpressionAttributeValues={
            ':r': ticketNum,
        }
    )
    print('posted the deal and ticketnum to Dynamo')

def parseJson(jsonObj):
    pass


if(True):
    with open('samplePostToDb.json') as f:
        awsJsonBody = json.load(f)
    event = {}
    event['body'] = json.dumps(awsJsonBody)
    result = lambda_handler(event,0)

