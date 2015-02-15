#!/usr/bin/python2
import MySQLdb, sys, json

usage = '''
rankings.py current
rankings.py contest <contest_name>
'''
from config import *
db = MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass, db=db_name)
cur = db.cursor(MySQLdb.cursors.DictCursor)

def scoreCmp(ai, bi):
    if ai['score'] > bi['score']:
        return -1
    elif ai['score'] < bi['score']:
        return 1
    else:
        if len(ai['solved']) > len(bi['solved']):
            return -1
        elif len(ai['solved']) < len(bi['solved']):
            return 1
        else:
            if ai['time'] + ai['penalty'] * 20 * 60 < bi['time'] + bi['penalty'] * 20 * 60:
                return -1
            elif ai['time'] + ai['penalty'] * 20 * 60 > bi['time'] + bi['penalty'] * 20 * 60:
                return 1
            else:
                return 0

def escape(string):
    return MySQLdb.escape_string(string)

def updateRanking(contestCode):
    query = "select * from contest where code = '%s'" % escape(contestCode);
    cur.execute(query)
    contest = cur.fetchone()
    if contest == None:
        raise Exception('No contest %s found!' % contestCode)
    query = """
    select runs.tid as tid, teamname, problems.score, submittime as time,
    (select count(rid) from runs r where tid = runs.tid and pid = runs.pid and result != 'AC'
    and result is not NULL and submittime < runs.submittime) as penalty, runs.pid as pid
    from runs, teams, problems, contest
    where
    teams.status = 'Normal' and runs.tid = teams.tid and problems.pid = runs.pid and
    runs.pid in (select pid from problems where pgroup ='%s') and result = 'AC' group by runs.tid, runs.pid
    """ % escape(contest['code'])
    cur.execute(query)
    rows = cur.fetchall()
    ranks = {}
    for row in rows:
        if row['tid'] in ranks:
            ranks[row['tid']]['time'] += row['time'] - contest['starttime']
            ranks[row['tid']]['score'] += row['score']
            ranks[row['tid']]['penalty'] += row['penalty']
            ranks[row['tid']]['solved'][row['pid']] = row['penalty']
        else:
            ranks[row['tid']] = {}
            ranks[row['tid']]['teamname'] = row['teamname']
            ranks[row['tid']]['time'] = (row['time'] - contest['starttime'])
            ranks[row['tid']]['score'] = row['score']
            ranks[row['tid']]['penalty'] = row['penalty']
            ranks[row['tid']]['solved'] = {row['pid']: row['penalty']}

    ret = []
    for tid, dic in ranks.items():
        dic['tid'] = tid
        ret.append(dic)

    ret.sort(scoreCmp)
    dataString = json.dumps(ret)
    query = 'UPDATE contest SET ranktable = "%s" WHERE code = "%s"' % (escape(dataString), escape(contest['code']))
    cur.execute(query)
    db.commit()
    return ret

if __name__ == '__main__':
    if len(sys.argv) == 2:
        if sys.argv[1] == 'current':
            cur.execute('SELECT value from admin WHERE variable="currentContest"')
            currentContest = cur.fetchone()['value']
            if currentContest == '':
                print 'No current contests'
                sys.exit(1)
            else:
                updateRanking(currentContest)
        else:
            print 'Invalid Argument'
            print usage
            sys.exit(1)
    elif len(sys.argv) == 3:
        if sys.argv[1] != 'contest':
            print'Invalid Argument'
            print usage
            sys.exit(1)
        updateRanking(sys.argv[2])

